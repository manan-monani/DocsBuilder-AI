<?php

namespace App\Http\Requests\Admin\Docs;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:doc_projects,name',
            'slug' => 'required|string|max:255|unique:doc_projects,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ];
    }
}
