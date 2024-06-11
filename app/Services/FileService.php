<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\DB;

class FileService
{
    public function store($file)
    {
        $file_hash = sha1_file($file->getPathname());
        $path = $file->storeAs('files', $file_hash, 'public');

        return DB::transaction(function () use ($file_hash, $file, $path) {
            return File::create([
                'name' => $file->getClientOriginalName(),
                'hash' => $file_hash,
                'size' => $file->getSize(),
                'path' => $path,
            ]);
        });
    }

    public function delete($file_id): void
    {
        $file = File::where('id', $file_id)->first();
        if ($file) {
            $file->delete();
        }
    }
}
