<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeatApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'preferred_floor' => ['nullable', 'integer', 'min:0'],
            'preferred_room_id' => ['nullable', 'exists:rooms,id'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
