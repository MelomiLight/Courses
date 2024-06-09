<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Http\Requests\CourseCreateRequest;
use App\Rules\EndDateAfterStartDate;

class Course extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Course::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title_en';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title_en', 'title_ru', 'title_kk', 'author'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Title (EN)', 'title_en')->sortable()->rules('required', 'max:255'),
            Text::make('Title (RU)', 'title_ru')->sortable()->rules('required', 'max:255'),
            Text::make('Title (KK)', 'title_kk')->sortable()->rules('required', 'max:255'),

            Textarea::make('Description (EN)', 'description_en')->rules('required'),
            Textarea::make('Description (RU)', 'description_ru')->rules('required'),
            Textarea::make('Description (KK)', 'description_kk')->rules('required'),

            DateTime::make('Start Date', 'start_date')
                ->rules('required'),

            DateTime::make('End Date', 'end_date')
                ->rules('required', new EndDateAfterStartDate(request()->input('start_date'))),

            Select::make('Format')->options([
                'online' => 'Online',
                'offline' => 'Offline',
            ])->rules('required'),

            Text::make('Author')->rules('required', 'max:255'),

            HasMany::make('Files'),

            HasMany::make('Comments'),
        ];
    }

    /**
     * Get the validation rules that apply to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public static function rules(NovaRequest $request)
    {
        return (new CourseCreateRequest())->rules();
    }
}
