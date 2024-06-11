<?php

namespace App\Nova;

use App\Services\FileService;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\File as NovaFile;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class File extends Resource
{
    public static $model = \App\Models\File::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'hash'
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            BelongsToMany::make('Courses', 'courses', Course::class)
                ->rules('required'), // Ensure course selection is mandatory
            Text::make('Name')->exceptOnForms(),
            Text::make('Hash')->exceptOnForms(),
            Number::make('Size')->exceptOnForms(),
            Text::make('Path')->exceptOnForms(),
            NovaFile::make('File')
                ->disk('public')
                ->storeAs(function (Request $request) {
                    return $request->file('file')->getClientOriginalName();
                })
                ->creationRules('required', 'file', 'mimes:pdf,jpg,png,pptx', 'max:20480')
                ->store(function (Request $request, $model) {
                    $fileService = app(FileService::class);

                    $file = $request->file('file');

                    // Use the FileService to store the file
                    $storedFile = $fileService->store($file);

                    // Attach the file to the selected course
                    if ($request->course) {
                        $course = \App\Models\Course::find($request->course);
                        $storedFile->courses()->attach($course->id);
                    }

                    return [
                        'path' => $storedFile->path,
                        'name' => $storedFile->name,
                        'hash' => $storedFile->hash,
                        'size' => $storedFile->size,
                    ];
                })
                ->delete(function ($request, $model) {
                    $fileService = app(FileService::class);

                    // Use the FileService to delete the file
                    $fileService->delete($model->id);
                    return [];
                }),
        ];
    }
}
