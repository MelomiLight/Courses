<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidIIN implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (strlen($value) !== 12 || !ctype_digit($value)) {
            return false;
        }

        $date = substr($value, 0, 6);
        if (!checkdate(substr($date, 2, 2), substr($date, 4, 2), '19' . substr($date, 0, 2))) {
            return false;
        }

        $centuryAndGender = intval(substr($value, 6, 1));
        if ($centuryAndGender < 0 || $centuryAndGender > 6) {
            return false;
        }

        $weights1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $weights2 = [3, 4, 5, 6, 7, 8, 9, 10, 11, 1, 2];

        $controlNumber = $this->calculateControlNumber($value, $weights1);

        if ($controlNumber === 10) {
            $controlNumber = $this->calculateControlNumber($value, $weights2);
        }

        if ($controlNumber === 10 || $controlNumber !== intval(substr($value, 11, 1))) {
            return false;
        }

        return true;
    }

    private function calculateControlNumber($value, $weights): int
    {
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum += intval($value[$i]) * $weights[$i];
        }
        return $sum % 11;
    }

    public function message(): string
    {
        return 'Incorrect IIN.';
    }
}
