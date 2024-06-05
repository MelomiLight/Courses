<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_kk' => $this->title_kk,
            'title_ru'=> $this->title_ru,
            'title_en'=> $this->title_en,
            'description_en'=> $this->description_en,
            'description_kk'=> $this->description_kk,
            'description_ru'=> $this->description_ru,
            'start_date'=> $this->start_date,
            'end_date'=> $this->end_date,
            'format'=> $this->format,
            'author'=> $this->author,
            'files' => FileResource::collection(optional($this->files)),
        ];
    }
}
