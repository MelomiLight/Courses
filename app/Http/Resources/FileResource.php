<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'course_id' => $this->course_id,
            'name' => $this->name,
            'hash' => $this->hash,
            'size' => $this->size,
            'path' => Storage::url($this->path),
        ];
    }
}
