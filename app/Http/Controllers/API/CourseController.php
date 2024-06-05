<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseStoreResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    private CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function store(CourseCreateRequest $request): JsonResponse|CourseStoreResource
    {
        try {
            $course = $this->service->store($request);
            if ($request->hasFile('files')) {
                $this->service->fileStore($request, $course);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return new CourseStoreResource($course);
    }

    public function show(Course $course): JsonResponse|CourseResource
    {
        try {
            $course->load('files');
            $user = Auth::guard('api')->user();
            if (!$user) {
                $course->author = null;
                $course->start_date = null;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return new CourseResource($course);
    }



}
