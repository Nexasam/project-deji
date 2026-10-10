<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'media' => ['sometimes', 'array', 'max:10'],
            'media.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:20480'],
            'youtube_url' => ['nullable', 'string', 'max:2048'],
        ];
    }

    /** @return list<\Illuminate\Http\UploadedFile> */
    public function mediaFiles(): array
    {
        return array_values($this->file('media', []));
    }

    public function youtubeUrl(): ?string
    {
        return $this->filled('youtube_url') ? $this->string('youtube_url')->trim()->toString() : null;
    }
}
