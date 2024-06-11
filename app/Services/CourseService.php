<?php

namespace App\Services;

use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    private FileService $service;

    public function __construct(FileService $service)
    {
        $this->service = $service;
    }

    public function store(CourseCreateRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['author'] = $request->user()->username;

        return DB::transaction(function () use ($validatedData, $request) {
            return Course::create($validatedData);
        });
    }

    public function update(CourseUpdateRequest $request, Course $course)
    {
        return DB::transaction(function () use ($course, $request) {
            return $course->update($request->except(['files']));
        });
    }

    public function delete(Course $course): void
    {
        DB::transaction(function () use ($course) {
            $course->delete();
        });
    }

    public function fileStore($request, Course $course): void
    {
        $files = $request->file('files');
        foreach ($files as $file) {
            $this->service->store($file);
            $course->files()->attach($file->id);
        }
    }

    public function fileUpdate($request, Course $course): void
    {
        $course->load('files');
        $existingFiles = $course->files;

        $existingFileHashes = $existingFiles->pluck('hash')->toArray();
        $newFiles = $request->file('files');
        if (is_null($newFiles)) {
            foreach ($existingFiles as $existingFile) {
                $this->service->delete($existingFile->id);
                $course->files()->detach($existingFile->id);
            }
            return;
        }
        $filesToKeep = [];

        foreach ($newFiles as $file) {
            $hash = $this->newFileHelper($file, $course, $existingFileHashes);
            if ($hash) {
                $filesToKeep[] = $hash;
            }
        }

        foreach ($existingFiles as $existingFile) {
            $this->existingFileHelper($existingFile, $filesToKeep, $course);
        }
    }

    public function newFileHelper($file, $course, $existingFileHashes): false|string
    {
        $hash = sha1_file($file->getPathname());
        if (!in_array($hash, $existingFileHashes)) {
            DB::transaction(function () use ($file, $course) {
                $storedFile = $this->service->store($file);
                $course->files()->attach($storedFile->id);
            });

            return false;
        }
        return $hash;
    }

    public function existingFileHelper($existingFile, $filesToKeep, $course): void
    {
        if (!in_array($existingFile->hash, $filesToKeep)) {
            $this->service->delete($existingFile->id);
            $course->files()->detach($existingFile->id);
        }
    }

}
