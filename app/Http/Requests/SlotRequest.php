<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust with policies when ready
    }

    public function rules(): array
    {
        return [
            'total_bricks' => ['required', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string'],
            'materials' => ['sometimes', 'array'],
            'materials.*.id' => ['required', 'exists:materials,id'],
            'materials.*.quantity' => ['required', 'integer', 'min:0'],
            'materials.*.price' => ['required', 'numeric', 'min:0'],
            'materials.*.added_at' => ['nullable', 'date'],
            'workers' => ['sometimes', 'array'],
            'workers.*.id' => ['required', 'exists:workers,id'],
            'workers.*.start_time' => ['nullable', 'date'],
            'workers.*.end_time' => ['nullable', 'date', 'after_or_equal:workers.*.start_time'],
            'workers.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
