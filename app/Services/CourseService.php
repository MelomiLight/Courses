<?php

namespace App\Services;

use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Models\Course;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            return $course->update($request->except(['files', 'delete_files']));
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
            $this->service->store($file, $course);
        }
    }

    public function fileDelete($request): void
    {
        $fileHashes = explode(',', $request->delete_files);
        foreach ($fileHashes as $fileHash) {
            $this->service->delete($fileHash);
        }
    }

}
