<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
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
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'title_ru' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'max:6000'],
            'description_kk' => ['nullable', 'max:6000'],
            'description_ru' => ['nullable', 'max:6000'],
            'start_date' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'format' => ['nullable', 'string', 'in:offline,online'],
            'files.*' => ['nullable', 'file', 'mimes:pdf,jpg,png,pptx', 'max:10240'],
            'delete_files' => ['nullable', 'string'],
        ];
    }
}
