<?php

namespace App\Http\Controllers;

use App\Libraries\FreshmailSubscriptionEmailHandler;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FreshmailController extends Controller
{
    public function catchWebhook(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        try {
            $freshmailHandler = new FreshmailSubscriptionEmailHandler($requestContent);
            $response = $freshmailHandler->handle();

            return response($response, 200);
        } catch (Exception $e) {
            Log::info($e);

            $response = $freshmailHandler->createResponse([], false);
            return response($response, 400);
        }
    }
}
