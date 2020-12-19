<?php

namespace App\Libraries\IAI;

use App\Libraries\StatusCodeSubscriber;

class SetClientIai extends IaiApi
{
    protected $address;

    public function __construct()
    {
        parent::__construct();
        $this->address = $this->target . "/api/?gate=clients/setClients/123/json";
    }


    public function updateApproval($status, array $clients)
    {
        $preparedData = $this->prepareDateToUpdateApproval($clients, $status);

        $params = [
            'params' => [
                'clients' => $preparedData
            ]
        ];
        return  $this->getResponse($params, true);
    }

    public function prepareDateToUpdateApproval($clients, $status)
    {
        $preparedData = [];

        foreach ($clients as $client) {
            $newData = [
                'login' => $client['login'],
                'email' => $client['email'],
                'email_newsletter' => $status == StatusCodeSubscriber::ACTIVE
            ];
            array_push($preparedData, $newData);
        }

        return $preparedData;
    }
}
