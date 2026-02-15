<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'student';
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
            'venue' => ['required', 'string', 'max:255'],
            'guest_speaker_name' => ['nullable', 'string', 'max:255'],
            'guest_speaker_designation' => ['nullable', 'string', 'max:255'],
            'faculty_mentor_id' => ['nullable', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_rate' => ['required', 'numeric', 'min:0'],
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
            'venue.required' => 'Venue is required.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.item_name.required' => 'Item name is required.',
            'items.*.quantity.required' => 'Item quantity is required.',
            'items.*.quantity.min' => 'Item quantity must be at least 1.',
            'items.*.unit_rate.required' => 'Item unit rate is required.',
            'items.*.unit_rate.min' => 'Item unit rate must be 0 or greater.',
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
            'faculty_mentor_id' => 'faculty mentor',
            'items.*.item_name' => 'item name',
            'items.*.quantity' => 'quantity',
            'items.*.unit_rate' => 'unit rate',
        ];
    }
}