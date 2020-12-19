<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class AuthWebhookIai
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private $hashIaiWebhookCampaign;

    public function __construct()
    {
        $this->hashIaiWebhookCampaign = config('app.hashIaiWebhookCampaign');
    }

    public function handle($request, Closure $next)
    {

        try {
            $isAuth = $this->checkAuthorized($request->header());
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            return response($response, 401);
        }

        if ($isAuth) {
            return $next($request);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Unauthorized'
            ];
            return response($response, 401);
        }
    }

    private function checkAuthorized($headers)
    {
        $hash = hash(
            'sha256',
            $headers['x-webhook-event-id'][0] .
                $headers['x-webhook-id'][0] .
                $headers['x-webhook-campaign-id'][0] .
                $this->hashIaiWebhookCampaign
        );

        return $headers['x-webhook-hash'][0] == $hash;
    }
}
