@php
    $user = auth()->user();
    $teamType = $user->currentTeamType();
    
    $tasks = \App\Models\Task::with(['assignedBy', 'event', 'media'])
        ->where(function($q) use ($user, $teamType) {
            $q->where('assigned_to_role', $user->role)
              ->orWhere('assigned_to_user_id', $user->id);
            
            if ($teamType) {
                $roleMapping = [
                    'graphics' => 'gd', 'photo' => 'photo', 'video' => 'video',
                    'smt' => 'smt', 'doc' => 'doc', 'decoration' => 'deco',
                ];
                $mappedRole = $roleMapping[$teamType] ?? $teamType;
                $q->orWhere('assigned_to_role', $mappedRole);
            }
        });

    $userTasks = (clone $tasks)->whereNotIn('status', ['completed', 'approved'])
        ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'rejected')")
        ->latest()
        ->get();
        
    $taskHistory = (clone $tasks)->whereIn('status', ['completed', 'approved'])
        ->latest()
        ->take(10)
        ->get();
@endphp

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
    <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-cause-purple-dark flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white tracking-tight">Assigned Tasks</h3>
                <p class="text-[10px] text-purple-200 uppercase tracking-widest font-black mt-0.5">Tasks assigned to your role</p>
            </div>
        </div>
        <span class="px-4 py-1.5 bg-white/20 text-white text-xs font-black rounded-full backdrop-blur-sm border border-white/30 uppercase tracking-wider">{{ $userTasks->count() }} Total</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned By</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($userTasks as $task)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->submission_notes)
                                <div class="mb-2"><strong class="text-xs text-gray-500">Submission:</strong><br>{{ $task->submission_notes }}</div>
                            @endif
                            @if($task->submission_file)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($task->submission_file) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        View Attached File
                                    </a>
                                </div>
                            @endif
                            @if($task->media->count() > 0)
                                <div class="mt-3">
                                    <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Uploaded Media ({{ $task->media->count() }})</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($task->media as $media)
                                            <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="block w-12 h-12 relative group" title="{{ $media->original_filename }}">
                                                @if($media->media_type === 'video')
                                                    <div class="w-full h-full bg-indigo-900 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                                    </div>
                                                @else
                                                    <img src="{{ Storage::url($media->file_path) }}" class="w-full h-full object-cover rounded border border-gray-200 hover:scale-110 transition-transform">
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($task->before_image || $task->after_image)
                                <div class="mt-3 flex space-x-4">
                                    @if($task->before_image)
                                        <div class="flex-1">
                                            <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Before</p>
                                            <a href="{{ Storage::url($task->before_image) }}" target="_blank">
                                                <img src="{{ Storage::url($task->before_image) }}" class="h-16 w-full object-cover rounded border hover:opacity-80 transition">
                                            </a>
                                        </div>
                                    @endif
                                    @if($task->after_image)
                                        <div class="flex-1">
                                            <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">After</p>
                                            <a href="{{ Storage::url($task->after_image) }}" target="_blank">
                                                <img src="{{ Storage::url($task->after_image) }}" class="h-16 w-full object-cover rounded border hover:opacity-80 transition">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($task->feedback)
                                <div><strong class="text-xs text-gray-500">Feedback:</strong><br>{{ $task->feedback }}</div>
                            @endif
                            @if($task->annotated_file)
                                <div class="mt-2">
                                    <strong class="text-xs text-red-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        President's Markup:
                                    </strong>
                                    <a href="{{ Storage::url($task->annotated_file) }}" target="_blank" class="mt-1 block">
                                        <img src="{{ Storage::url($task->annotated_file) }}" alt="Annotated Feedback" class="h-20 object-cover rounded border border-red-200 hover:opacity-80 transition">
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'No deadline' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-700 mr-2">
                                    {{ substr($task->assignedBy->name ?? '?', 0, 1) }}
                                </div>
                                <div class="text-xs text-gray-700">{{ $task->assignedBy->name ?? 'System' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($task->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                            @elseif($task->status === 'in_progress')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($task->status === 'completed' || $task->status === 'approved' || $task->status === 'rejected')
                                <span class="text-xs text-gray-500">Submitted</span>
                                @if($task->feedback)
                                    <button onclick="alert('President Feedback: ' + `{{ addslashes($task->feedback) }}`)" class="ml-2 text-indigo-600 hover:text-indigo-800 text-xs underline">View Feedback</button>
                                @endif
                                @if($task->status === 'rejected')
                                    <button onclick="document.getElementById('submit-modal-{{ $task->id }}').classList.remove('hidden')" class="ml-2 text-red-600 hover:text-red-800 text-xs underline">Resubmit</button>
                                @endif
                            @else
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="inline-block mb-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="text-xs bg-yellow-50 text-yellow-600 border border-yellow-200 px-2 py-1 rounded hover:bg-yellow-100" @disabled($task->status === 'in_progress')>
                                        {{ $task->status === 'in_progress' ? 'In Progress' : 'Start Task' }}
                                    </button>
                                </form>
                                <button type="button" onclick="document.getElementById('submit-modal-{{ $task->id }}').classList.remove('hidden')" class="text-xs bg-green-50 text-green-600 border border-green-200 px-2 py-1 rounded hover:bg-green-100">
                                    Submit
                                </button>
                            @endif
                        </td>

                            <!-- Submit Modal -->
                            <div id="submit-modal-{{ $task->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('submit-modal-{{ $task->id }}').classList.add('hidden')"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Submit Task: {{ $task->title }}</h3>
                                                <div class="mt-4">
                                                    <label class="block text-sm font-medium text-gray-700">Submission Notes / Link</label>
                                                    <textarea name="submission_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add any notes or links to your work here..." required></textarea>
                                                    <p class="text-xs text-gray-500 mt-1">Provide details of your work so the President can review it.</p>
                                                </div>
                                                <div class="mt-4">
                                                    @if(in_array($task->assigned_to_role, ['photo', 'video']))
                                                        <label class="block text-sm font-medium text-gray-700">Upload Media (Multiple Images/Videos) *</label>
                                                        <div class="mt-1 border-2 border-dashed border-indigo-200 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors cursor-pointer group" onclick="this.querySelector('input').click()">
                                                            <div class="flex flex-col items-center">
                                                                <svg class="h-10 w-10 text-indigo-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <p class="text-sm text-indigo-600 font-bold mt-2">Click to select files</p>
                                                                <p id="file-count-{{ $task->id }}" class="text-xs text-gray-500 mt-1">Select multiple photos or videos</p>
                                                            </div>
                                                            <input type="file" name="submission_files[]" multiple accept="image/*,video/*" class="hidden" onchange="handleFileSelect(this, {{ $task->id }})" required>
                                                        </div>
                                                        <div id="preview-grid-{{ $task->id }}" class="grid grid-cols-6 gap-2 mt-3"></div>
                                                        <p class="text-[10px] text-gray-400 mt-1 italic">Note: Select all files at once. Max 50MB total.</p>
                                                    @else
                                                        <label class="block text-sm font-medium text-gray-700">Attach File (Word, PDF, Image) - Optional</label>
                                                        <input type="file" name="submission_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                    @endif
                                                </div>
                                                @if($task->assigned_to_role === 'deco')
                                                    <div class="mt-4 grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Before Image *</label>
                                                            <input type="file" name="before_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100" required>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">After Image *</label>
                                                            <input type="file" name="after_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Submit Work
                                                </button>
                                                <button type="button" onclick="document.getElementById('submit-modal-{{ $task->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p>No tasks assigned to your team yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($taskHistory->count() > 0)
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8 opacity-75 grayscale-[0.5] hover:grayscale-0 transition-all">
    <div class="px-6 py-3 bg-gray-50 flex justify-between items-center border-b border-gray-100">
        <div class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-widest">Task History (Recent)</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <tbody class="divide-y divide-gray-100">
                @foreach($taskHistory as $history)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <div class="text-sm font-bold text-gray-700">{{ $history->title }}</div>
                            <div class="text-[10px] text-gray-400">Approved on {{ $history->updated_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $history->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $history->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@if(!isset($taskScriptInjected))
    @php $taskScriptInjected = true; @endphp
    <script>
    function handleFileSelect(input, taskId) {
        const files = input.files;
        const previewGrid = document.getElementById(`preview-grid-${taskId}`);
        const countLabel = document.getElementById(`file-count-${taskId}`);
        const dropzone = input.parentElement;
        
        previewGrid.innerHTML = '';
        let totalSize = 0;
        const maxSize = 50 * 1024 * 1024; // 50MB
        
        if (files.length > 50) {
            alert("Maximum 50 files allowed.");
            input.value = '';
            return;
        }
        
        for (const file of files) {
            totalSize += file.size;
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "w-full h-10 object-cover rounded border border-indigo-200";
                    previewGrid.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const div = document.createElement('div');
                div.className = "w-full h-10 bg-indigo-900 rounded flex items-center justify-center text-[8px] text-white font-bold border border-indigo-300";
                div.innerText = "VIDEO";
                previewGrid.appendChild(div);
            }
        }
        
        if (totalSize > maxSize) {
            alert("Total file size exceeds 50MB limit. Current size: " + (totalSize / (1024 * 1024)).toFixed(1) + " MB");
            input.value = '';
            previewGrid.innerHTML = '';
            countLabel.textContent = 'Select multiple photos or videos';
            dropzone.classList.remove('bg-indigo-50');
            return;
        }
        
        countLabel.textContent = files.length + ' files selected (' + (totalSize / (1024 * 1024)).toFixed(1) + ' MB)';
        countLabel.classList.add('text-indigo-700', 'font-bold');
        dropzone.classList.add('bg-indigo-50');
    }
    </script>
@endif
