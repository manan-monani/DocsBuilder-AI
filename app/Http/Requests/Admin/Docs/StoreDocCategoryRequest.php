<?php

namespace App\Http\Requests\Admin\Docs;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocCategoryRequest extends FormRequest
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
            'doc_version_id' => 'required|exists:doc_versions,id',
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_categories')->where(fn ($query) => $query->where('doc_version_id', $this->doc_version_id)),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_categories')->where(fn ($query) => $query->where('doc_version_id', $this->doc_version_id)),
            ],
            'order' => 'integer',
            'icon' => 'nullable|string|max:255',
        ];
    }
}
