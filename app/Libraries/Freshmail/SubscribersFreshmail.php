<?php

namespace App\Libraries\Freshmail;

use App\Libraries\Freshmail\Exceptions\EmailAddresIsIncorrect;
use App\Libraries\Freshmail\Exceptions\SubscriberDoesntExist;
use App\Libraries\StatusCodeSubscriber;

class SubscribersFreshmail extends FreshmailApi
{
    public $listApi;
    public $email;

    public function __construct()
    {
        parent::__construct();
        $this->listApi = new SubscriberListFreshmail();
    }

    public function get($list, $email)
    {
        $hash = $this->listApi->getHashList($list);
        $this->address = "/rest/subscriber/get/$hash/$email";

        return $this->sendGet();
    }

    public function hasApproval($list, $email): int
    {
        try {
            $subscriber = $this->get($list, $email);
            return $subscriber['data']['state'];
        } catch (EmailAddresIsIncorrect $e) {
            return StatusCodeSubscriber::REMOVED;
        }
    }

    public function updateApproval($status, $email, $list)
    {
        try {
            $this->setStatus($status, $email, $list);
        } catch (SubscriberDoesntExist $e) {
            $this->add($status, $email, $list);
        }
    }

    public function setStatus($status, $email, $list)
    {
        $this->address = "/rest/subscriber/edit";

        $this->request = [
            'email' => $email,
            'list' => $this->listApi->getHashList($list),
            'state' => $status
        ];

        $this->sendPost();
    }

    public function add($status, $email, $list)
    {

        $this->address =  "/rest/subscriber/add";

        $this->request = [
            'email' => $email,
            'list' => $this->listApi->getHashList($list),
            'state' => $status
        ];

        $this->sendPost();
    }
}
