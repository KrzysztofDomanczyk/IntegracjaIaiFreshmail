<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorApiIai;
use App\Libraries\Freshmail\SubscribersFreshmail;
use App\Libraries\IaiSubscrptionEmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IaiController extends Controller
{


    public function catchWebhook(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        try {
            $iaiHandler = new IaiSubscrptionEmailHandler($requestContent);
            $response = $iaiHandler->handle();

            return response($response, 200);
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];

            return response($response, 400);
        }
    }
}


