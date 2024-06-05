<?php

namespace App\Services;

use App\Http\Requests\CourseCreateRequest;
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

    public function fileStore(CourseCreateRequest $request, Course $course): void
    {
        $files = $request->file('files');
        foreach ($files as $file) {
            $this->service->store($file, $course);
        }
    }

}
