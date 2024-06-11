<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseAllResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    private CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function store(CourseCreateRequest $request): JsonResponse
    {
        try {
            $course = $this->service->store($request);
            if ($request->hasFile('files')) {
                $this->service->fileStore($request, $course);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json([
            'message' => __('messages.create.success', ['attribute' => 'course']),
            'data' => new CourseAllResource($course),
        ]);

    }

    public function show(Course $course): JsonResponse|CourseResource
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                $course->author = null;
                $course->start_date = null;
            }

            $course->load('files');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return new CourseResource($course);
    }


    public function index(): AnonymousResourceCollection
    {
        $courses = Course::with('files')->get();

        return CourseResource::collection($courses);
    }

    public function update(CourseUpdateRequest $request, Course $course): JsonResponse
    {
        try {
            $this->service->update($request, $course);

            $this->service->fileUpdate($request, $course);

            $course->load('files');

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => __('messages.update.success', ['attribute' => 'course']),
            'data' => new CourseAllResource($course),
        ]);
    }

    public function delete(Course $course): JsonResponse
    {
        $this->service->delete($course);

        return response()->json(['message' => __('messages.delete.success', ['attribute' => 'course'])]);
    }

}
