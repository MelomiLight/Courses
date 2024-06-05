<?php

namespace App\Http\Requests;

use App\Rules\OneOfFields;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => ['nullable', 'string', 'exists:users,username'],
            'iin' => ['nullable', 'string', 'exists:users,iin'],
            'email' => ['nullable', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'only_one' => [new OneOfFields()]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['only_one' => true]);
    }
}
