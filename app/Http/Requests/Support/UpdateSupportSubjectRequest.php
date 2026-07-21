<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;
        $category = $this->integer('category');

        return [
            'category' => ['required', 'integer', 'min:1', 'max:9999'],
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('support_subjects', 'name')
                    ->where(fn ($query) => $query->where('category', $category))
                    ->ignore($subjectId),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
