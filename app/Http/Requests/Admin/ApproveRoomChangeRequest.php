<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApproveRoomChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target_seat_id' => ['required', 'integer', 'exists:seats,id'],
            'admin_comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
