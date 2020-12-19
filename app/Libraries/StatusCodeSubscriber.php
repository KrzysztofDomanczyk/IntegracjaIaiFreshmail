<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class StatusCodeSubscriber
{
    const ACTIVE = 1;
    const TOACTIVE = 2;
    const INACTIVE = 3;
    const REMOVED = 4;
}
