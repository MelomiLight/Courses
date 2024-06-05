<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

class OneOfFields implements Rule
{
    protected $request;

    public function __construct()
    {
        $this->request = request();
    }

    public function passes($attribute, $value)
    {
        $fields = ['username', 'email', 'iin'];
        $filledFields = 0;

        foreach ($fields as $field) {
            if ($this->request->filled($field)) {
                $filledFields++;
            }
        }

        return $filledFields === 1;
    }

    public function message()
    {
        return 'Only one of username, email, or iin should be provided.';
    }
}
