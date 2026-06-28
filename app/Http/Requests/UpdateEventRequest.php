<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = $this->route('event');
        
        // Only the student who created the event can update it
        // And only if it's still pending_president status
        return $this->user() && 
               $this->user()->id === $event->student_id && 
               $event->status === 'pending_president';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'expected_date' => ['required', 'date', 'after:today'],
            'venue' => ['nullable', 'string', 'max:255'],
            'guest_speaker_name' => ['nullable', 'string', 'max:255'],
            'guest_speaker_designation' => ['nullable', 'string', 'max:255'],
            'guest_speaker_profile_link' => ['nullable', 'url', 'max:255'],
            'faculty_mentor_id' => ['nullable', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.total_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'description.required' => 'Event description is required.',
            'expected_date.required' => 'Expected date is required.',
            'expected_date.after' => 'Expected date must be in the future.',

            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.item_name.required' => 'Item name is required.',
            'items.*.quantity.required' => 'Item quantity is required.',
            'items.*.quantity.min' => 'Item quantity must be at least 1.',
            'items.*.total_amount.required' => 'Item amount is required.',
            'items.*.total_amount.min' => 'Item amount must be 0 or greater.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'expected_date' => 'expected date',
            'guest_speaker_name' => 'guest speaker name',
            'guest_speaker_designation' => 'guest speaker designation',
            'guest_speaker_profile_link' => 'guest speaker profile link',
            'faculty_mentor_id' => 'faculty mentor',
            'items.*.item_name' => 'item name',
            'items.*.quantity' => 'quantity',
            'items.*.total_amount' => 'amount',
        ];
    }
}