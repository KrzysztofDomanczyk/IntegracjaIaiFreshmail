<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ErrorApiFreshmail extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Error API Freshmail');
    }
}
