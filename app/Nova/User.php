<?php

namespace App\Nova;

use App\Rules\ValidIIN;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'username';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'username', 'email'
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

            Text::make('First Name', 'first_name')->rules('required', 'max:255'),
            Text::make('Patronymic', 'patronymic')->nullable()->rules('max:255'),
            Text::make('Last Name', 'last_name')->rules('required', 'max:255'),
            Text::make('Username')->rules('required', 'max:255')->sortable(),
            Text::make('IIN')->rules('required', 'max:12', new ValidIIN)->sortable(),
            Text::make('Email')->rules('required', 'email', 'max:255')->sortable(),
            Boolean::make('Role')->rules('required'),
            Password::make('Password')->onlyOnForms()->rules('required', 'min:8'),

            HasMany::make('Comments')
        ];
    }
}
