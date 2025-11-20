<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' =>[
                'sometimes',
                'required',
                'email',
                'unique:users,email,' . $this->route('user')->id,
            ],
            'phone' => 'sometimes|required|string|max:20',
            'department' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|in:user,admin,manager,editor',
            'status' => 'sometimes|required|in:active,inactive,pending',
        ];

    }
    

}
