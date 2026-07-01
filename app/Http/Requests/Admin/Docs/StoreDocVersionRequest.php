<?php

namespace App\Http\Requests\Admin\Docs;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocVersionRequest extends FormRequest
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
            'doc_project_id' => 'required|exists:doc_projects,id',
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_versions')->where(fn ($query) => $query->where('doc_project_id', $this->doc_project_id)),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_versions')->where(fn ($query) => $query->where('doc_project_id', $this->doc_project_id)),
            ],
            'is_default' => 'boolean|declined_if:is_archived,true',
            'is_archived' => 'boolean|declined_if:is_default,true',
        ];
    }

    public function messages(): array
    {
        return [
            'is_default.declined_if' => 'The default version cannot be archived.',
            'is_archived.declined_if' => 'An archived version cannot be the default version.',
        ];
    }
}
