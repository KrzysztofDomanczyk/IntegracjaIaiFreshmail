<?php

namespace App\Libraries\IAI;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class IaiApi extends Auth
{

    public $target = 'https://phu-szczepan.iai-shop.com';
    protected $products;
    protected $response;
    protected $request;
    protected $shopId;

    public function __construct()
    {
        parent::__construct();
        $this->shopId = config('app.shopId');
    }

    protected function getResponse($params, $old = false)
    {

        $this->request = [
            'authenticate' => $old == false ? $this->getNewAuthMethod() :  $this->getOldAuthMethod(),
            key($params) => $params[key($params)]
        ];


        return $this->send();
    }

    private function getNewAuthMethod()
    {
        return  [
            'userLogin' =>  $this->getLogin(),
            'authenticateKey' => $this->getAuthenticatedKey()
        ];
    }

    private function getOldAuthMethod()
    {
        return [
            'system_key' =>  $this->getAuthenticatedKey(),
            'system_login' => $this->getLogin()
        ];
    }

    protected function send()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json;charset=UTF-8'
        ])->post($this->address, $this->request);

        if ($this->isError($response)) {
            $this->throwErrorApiException('ERROR from API');
        };
        $this->response = $response->json();
        
        return $this->response;
    }

    protected function throwErrorApiException($message = null)
    {
        $exceptionMessage = [
            'response' => $this->response,
            'request' => $this->request,
            'date' => Carbon::now()
        ];

        throw new \App\Exceptions\ErrorApiIai("Błąd IAI - $message" . json_encode($exceptionMessage));
    }

    private function isError($response)
    {
        return $response['errors']['faultCode'] != 0;
    }

    protected function returnForShopId($array)
    {
        $array = array_filter(
            $array,
            function ($k) {
                if ($k['shop_id'] == $this->shopId) {
                    return $k;
                }
            },
            ARRAY_FILTER_USE_BOTH
        );
        return array_shift($array);
    }
}
