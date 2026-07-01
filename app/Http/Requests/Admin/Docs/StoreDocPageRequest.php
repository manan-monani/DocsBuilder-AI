<?php

namespace App\Http\Requests\Admin\Docs;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocPageRequest extends FormRequest
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
            'doc_category_id' => 'required|exists:doc_categories,id',
            'title' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_pages')->where(fn ($query) => $query->where('doc_category_id', $this->doc_category_id)),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('doc_pages')->where(fn ($query) => $query->where('doc_category_id', $this->doc_category_id)),
            ],
            'content_html' => 'required|string',
            'content_json' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'required|in:draft,published,archived',
            'change_summary' => 'nullable|string|max:255',
        ];
    }
}
