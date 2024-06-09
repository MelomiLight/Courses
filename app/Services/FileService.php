<?php

namespace App\Services;

use App\Models\Course;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function store($file, Course $course)
    {
        $path = $file->storeAs('files', $file->hashName(), 'public');

        return DB::transaction(function () use ($file, $course, $path) {
            File::create([
                'course_id' => $course->id,
                'name' => $file->getClientOriginalName(),
                'hash' => $file->hashName(),
                'size' => $file->getSize(),
                'path' => $path,
            ]);
        });
    }

    public function delete($fileHash): void
    {
        $file = File::where('hash', trim($fileHash))->first();
        if ($file) {
            $file->delete();
        }
    }
}
