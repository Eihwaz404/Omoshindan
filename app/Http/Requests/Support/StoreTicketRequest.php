<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public const DESCRIPTION_MIN_LENGTH = 20;
    public const DESCRIPTION_MAX_LENGTH = 1000;
    public const ATTACHMENTS_MAX_COUNT = 8;
    public const ATTACHMENT_MAX_SIZE_KB = 1536;

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
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'integer', Rule::exists('support_subjects', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'description' => ['required', 'string', 'min:'.self::DESCRIPTION_MIN_LENGTH, 'max:'.self::DESCRIPTION_MAX_LENGTH],
            'area_id' => ['required', 'integer', Rule::exists('support_areas', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'images' => ['nullable', 'array', 'max:'.self::ATTACHMENTS_MAX_COUNT],
            'images.*' => ['nullable', 'file', 'mimes:jpg,jpeg', 'mimetypes:image/jpeg,image/pjpeg', 'max:'.self::ATTACHMENT_MAX_SIZE_KB],
        ];
    }
}
