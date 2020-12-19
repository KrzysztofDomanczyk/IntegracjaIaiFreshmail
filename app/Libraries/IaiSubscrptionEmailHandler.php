<?php

namespace App\Libraries;

use App\Exceptions\SubscriberDoesntExistFreshmail;
use App\Libraries\StatusCodeSubscriber;
use App\Libraries\Freshmail\SubscribersFreshmail;
use App\Libraries\IAI\GetClientIai;
use App\Libraries\IAI\IaiApi;

class IaiSubscrptionEmailHandler
{
    public $status;
    public $messageError;
    private $requestContent;
    private $mailingSystem;
    private $approvalIai;
    private $approvalMailingSystem;
    private $updated;

    public function __construct($requestContent)
    {
        $this->requestContent = $requestContent;
        $this->mailingSystem = new SubscribersFreshmail();
    }
    public function handle()
    {
        $this->updated = false;
        if ($this->isDiffrenceApprovalBetweenMailingSystems()) {
            $this->updateApproval($this->approvalIai);
        }

        return [
            'status' => 'correct',
            'message' => [
                'updated' => $this->updated,
                'email' => $this->requestContent['email'],
                'approvalIai' => $this->approvalIai,
                'approvalMailingSystem' => $this->approvalMailingSystem,
            ]
        ];
    }

    private function isDiffrenceApprovalBetweenMailingSystems()
    {
        $this->approvalIai = $this->isClientApprovalIai();
        $this->approvalMailingSystem = $this->isClientApprovalMailingSystem($this->requestContent['email']);

        return $this->approvalIai != $this->approvalMailingSystem;
    }

    private function isClientApprovalMailingSystem()
    {
        return $this->mailingSystem->hasApproval(config('app.listName'), $this->requestContent['email']);
    }

    private function isClientApprovalIai()
    {
        $client = new GetClientIai();
        return $client->hasApproval($this->requestContent['id'], $this->requestContent['email']);
    }

    private function updateApproval($status)
    {
        $this->mailingSystem->updateApproval($status, $this->requestContent['email'], config('app.listName'));
        $this->updated = true;
    }
}
