<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EndDateAfterStartDate implements Rule
{
    private $start_date;

    public function __construct($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return strtotime($value) > strtotime($this->start_date);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The end date must be greater than the start date.';
    }
}
