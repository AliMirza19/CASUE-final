<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        // Only President or VC can assign system tasks
        if (Auth::user()->role !== 'president' && Auth::user()->role !== 'vc') {
            return back()->with('error', 'Only the President or Volunteer Coordinator is authorized to assign tasks.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to_role' => 'required|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'event_id' => 'nullable|exists:events,id',
            'due_date' => 'nullable|date',
        ]);

        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to_role' => $request->assigned_to_role,
                'assigned_to_user_id' => $request->assigned_to_user_id,
                'assigned_by_user_id' => Auth::id(),
                'event_id' => $request->event_id,
                'due_date' => $request->due_date,
                'status' => 'pending',
            ]);
            
            if ($request->assigned_to_user_id) {
                $user = \App\Models\User::find($request->assigned_to_user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\EventStatusUpdated(
                        $task->event ?? new \App\Models\Event(),
                        "The President has assigned you a new task: {$task->title}",
                        'info'
                    ));
                }
            }

            return back()->with('success', 'Task assigned successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return back()->with('error', 'A similar task has already been assigned for this role and event.');
            }
            return back()->with('error', 'Failed to assign task.');
        }
    }

    /**
     * Update the status of a task.
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Status Check: Block updates if already approved (unless President is reviewing)
        $isPresidentReview = in_array($request->status, ['approved', 'rejected']);
        if ($task->status === 'approved' && !$isPresidentReview) {
            return back()->with('error', 'This task is already approved and cannot be modified.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,approved,rejected',
            'submission_notes' => 'nullable|string',
            'submission_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'submission_files' => 'nullable|array|max:50', // Max 50 files
            'submission_files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mov,avi,webp|max:51200', // 50MB max
            'before_image' => 'nullable|image|max:10240',
            'after_image' => 'nullable|image|max:10240',
            'feedback' => 'nullable|string',
        ]);

        // President reviewing task
        if (in_array($request->status, ['approved', 'rejected'])) {
            if (Auth::user()->role !== 'president' && !Auth::user()->isAdmin()) {
                return back()->with('error', 'Only the President can review and approve/reject tasks.');
            }
            
            $updateData = [
                'status' => $request->status,
                'feedback' => $request->feedback,
            ];
            
            // Handle base64 annotated image
            if ($request->filled('annotated_image')) {
                $base64Image = $request->input('annotated_image');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, etc.
                    
                    if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $base64Image = str_replace(' ', '+', $base64Image);
                        $imageName = 'task_submissions/annotated_' . $task->id . '_' . time() . '.' . $type;
                        
                        \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($base64Image));
                        $updateData['annotated_file'] = $imageName;
                    }
                }
            }
            
            $task->update($updateData);

            if ($request->status === 'approved') {
                $this->archiveTaskAssets($task);
            }
            
            return back()->with('success', 'Task has been ' . $request->status . '.');
        }

        // Teams updating task
        if (Auth::user()->role !== $task->assigned_to_role && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $updateData = [
            'status' => $request->status,
            'assigned_to_user_id' => Auth::id(), // Take ownership of the task when updating
        ];
        
        if ($request->has('submission_notes')) {
            $updateData['submission_notes'] = $request->submission_notes;
        }

        $folder = "task_submissions/{$task->id}";

        if ($request->hasFile('submission_file')) {
            $path = $request->file('submission_file')->store($folder, 'public');
            $updateData['submission_file'] = $path;
        }

        if ($request->hasFile('before_image')) {
            $updateData['before_image'] = $request->file('before_image')->store($folder, 'public');
        }

        if ($request->hasFile('after_image')) {
            $updateData['after_image'] = $request->file('after_image')->store($folder, 'public');
        }

        if ($request->hasFile('submission_files')) {
            foreach ($request->file('submission_files') as $file) {
                $path = $file->store($folder, 'public');
                $extension = strtolower($file->getClientOriginalExtension());
                $mediaType = in_array($extension, ['mp4', 'mov', 'avi']) ? 'video' : 'photo';
                
                \App\Models\EventMedia::create([
                    'event_id' => $task->event_id ?? null,
                    'task_id' => $task->id,
                    'uploaded_by' => Auth::id(),
                    'media_type' => $mediaType,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $task->update($updateData);

        return back()->with('success', 'Task status updated to ' . str_replace('_', ' ', $request->status) . '!');
    }

    /**
     * Archive task assets to the Institutional Memory (EventDocument)
     */
    private function archiveTaskAssets(Task $task)
    {
        if (!$task->event_id) return;

        $roleToDocType = [
            'gd'    => 'poster_graphic',
            'photo' => 'event_media',
            'video' => 'event_media',
            'doc'   => 'general_documentation',
            'smt'   => 'general_documentation',
            'deco'  => 'general_documentation',
        ];

        $docType = $roleToDocType[$task->assigned_to_role] ?? 'general_documentation';

        $assets = [
            'submission_file' => 'Final Submission',
            'before_image'    => 'Before Photos',
            'after_image'     => 'After Photos'
        ];

        foreach ($assets as $field => $desc) {
            if ($task->$field) {
                \App\Models\EventDocument::updateOrCreate(
                    [
                        'event_id' => $task->event_id,
                        'file_path' => $task->$field,
                    ],
                    [
                        'uploaded_by'       => $task->assigned_to_user_id ?? Auth::id(),
                        'doc_type'          => $docType,
                        'original_filename' => basename($task->$field),
                        'description'       => "Task: {$task->title} - {$desc} ({$task->assigned_to_role})",
                        'visible_to_roles'  => ['admin', 'president', 'hod', 'patron'],
                    ]
                );
            }
        }
    }

    /**
     * Bulk store tasks for multiple roles (used when president approves an event).
     */
    public function bulkStore(Request $request)
    {
        if (Auth::user()->role !== 'president') {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tasks' => 'required|array',
            'tasks.*.role' => 'required|string',
            'tasks.*.title' => 'nullable|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.due_date' => 'nullable|date',
        ]);

        $assignedCount = 0;

        foreach ($request->tasks as $taskData) {
            // Skip empty titles
            if (empty($taskData['title'])) continue;

            try {
                Task::create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'] ?? '',
                    'assigned_to_role' => $taskData['role'],
                    'assigned_by_user_id' => Auth::id(),
                    'event_id' => $request->event_id,
                    'due_date' => $taskData['due_date'] ?? null,
                    'status' => 'pending',
                ]);
                $assignedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore duplicates
            }
        }

        return back()->with('success', "{$assignedCount} tasks assigned successfully!");
    }
}
