<?php

namespace App\Libraries\IAI;

use App\Libraries\StatusCodeSubscriber;

class GetClientIai extends IaiApi
{
    protected $address;

    public function __construct()
    {
        parent::__construct();
        $this->address = $this->target . "/api/?gate=clients/getClients/122/json";
    }

    public function getClientById($id)
    {

        $params = [
            'params' => [
                'clients' => [
                    'client_numbers' => [
                        $id
                    ]
                ]
            ]
        ];

        $response = $this->getResponse($params, true);


        return array_shift($response['clients']);
    }

    public function hasApproval($id, $email)
    {

        $client = $this->getClientById($id);

        $this->checkEmailForClient($client, $email);

        $dateApproval = $this->returnForShopId($client['newsletter_email_approvals']);

        $approval = $dateApproval['approval'];

        switch ($approval) {
            case '0000-00-00 00:00:00':
                return StatusCodeSubscriber::REMOVED;
                break;
            default:
                return StatusCodeSubscriber::ACTIVE;
                break;
        }
    }

    public function getClientsByEmail($email)
    {

        $params = [
            'params' => [
                'text_search' => "$email"
            ]
        ];

        $response = $this->getResponse($params, true);

        foreach ($response['clients'] as $key => $client) {
            if ($client['email'] != $email) {
                unset($response['clients'][$key]);
            }
        }

        return array_values($response['clients']);
    }

    private function checkEmailForClient($client, $email)
    {
        if ($client['email'] != $email) {
            $response = "Niepoprawny adres e-mail: $email u klienta o id " . $client['client_number'];

            throw new \App\Exceptions\IncorrectEmailInClientIAI($response);
        }
    }
}
