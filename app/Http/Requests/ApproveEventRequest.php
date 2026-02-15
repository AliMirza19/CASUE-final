<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = $this->route('event');
        $user = $this->user();
        
        if (!$user) {
            return false;
        }

        // Check if user has the right role for the current event status
        return match ($event->status) {
            'pending_president' => $user->role === 'president',
            'pending_patron' => $user->role === 'patron',
            'pending_hod' => $user->role === 'hod',
            'pending_sa' => $user->role === 'sa',
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'string', 'max:500'],
            'patron_comments' => ['nullable', 'string', 'max:500'], // For patron-specific feedback
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Please specify whether to approve or reject.',
            'action.in' => 'Action must be either approve or reject.',
            'rejection_reason.required_if' => 'Rejection reason is required when rejecting an event.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 500 characters.',
            'patron_comments.max' => 'Comments cannot exceed 500 characters.',
        ];
    }
}