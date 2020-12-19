<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class IncorrectEmailInClientIAI extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
    }
}
