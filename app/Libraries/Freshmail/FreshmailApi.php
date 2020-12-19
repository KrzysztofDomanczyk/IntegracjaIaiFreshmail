<?php

namespace App\Libraries\Freshmail;

use App\Libraries\Freshmail\Exceptions\EmailAddresIsIncorrect;
use App\Libraries\Freshmail\Exceptions\SubscriberDoesntExist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class FreshmailApi
{
    public $target = 'https://api.freshmail.com';
    private $headers;
    protected $response;
    protected $request;
    protected $address;

    public function __construct()
    {
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('app.secretKeyFreshmail')
        ];
    }
    public function pingPOST()
    {
        $this->address = '/rest/ping';
        $this->request = [
            'test' => 'test'
        ];

        return $this->sendPost();
    }

    public function pingGET()
    {
        $this->address = '/rest/ping';

        return $this->sendGet();
    }

    protected function sendPost()
    {
        $url = $this->target . $this->address;

        $response = Http::withHeaders($this->headers)->post($url, $this->request);

        $this->response = $response->json();

        if ($this->isError($response)) {
            $this->throwSpecificException();
        };

        return $this->response;
    }

    protected function sendGet()
    {
        $url = $this->target  . $this->address;

        $response = Http::withHeaders($this->headers)->get($url);

        $this->response = $response->json();

        if ($this->isError($response)) {
            $this->throwSpecificException();
        };
        return $this->response;
    }

    protected function throwErrorException($message = null)
    {
        $exceptionMessage = [
            'response' => $this->response,
            'request' => $this->request,
            'date' => Carbon::now()
        ];

        throw new \App\Exceptions\ErrorApiFreshmail("Błąd Freshmail - $message" . json_encode($exceptionMessage));
    }

    private function isError()
    {
        return $this->response['status'] == 'ERROR';
    }

    private function throwSpecificException()
    {

        $excpetionArray = [
            1311 => new EmailAddresIsIncorrect(),
            1331 => new SubscriberDoesntExist(),
        ];

        foreach ($this->response['errors'] as $error) {
            throw $excpetionArray[$error['code']];
        }
    }
}
