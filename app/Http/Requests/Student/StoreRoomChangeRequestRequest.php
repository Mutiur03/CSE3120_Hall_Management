<?php

namespace App\Http\Requests\Student;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomChangeRequestRequest extends FormRequest
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
            'requested_room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where('status', RoomStatus::Active->value),
            ],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
