<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File as NovaFile;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\Storage;

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
            BelongsTo::make('Course'),
            Text::make('Name')->exceptOnForms(),
            Text::make('Hash')->exceptOnForms(),
            Number::make('Size')->exceptOnForms(),
            Text::make('Path')->exceptOnForms(),
            NovaFile::make('File')
                ->disk('public')
                ->storeAs(function (Request $request) {
                    return $request->file->getClientOriginalName();
                })
                ->creationRules('required', 'file', 'mimes:pdf,jpg,png,pptx', 'max:20480')
                ->store(function (Request $request, $model) {
                    $file = $request->file('file');
                    $path = $file->storeAs('files', $file->hashName(), 'public');

                    return [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'hash' => $file->hashName(),
                        'size' => $file->getSize(),
                    ];
                })
                ->delete(function (Request $request, $model) {
                    Storage::disk('public')->delete($model->path);
                    return [];
                }),
        ];
    }
}
