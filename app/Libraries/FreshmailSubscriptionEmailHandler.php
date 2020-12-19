<?php

namespace App\Libraries;

use App\Exceptions\ErrorApiIai;
use App\Exceptions\SubscriberDoesntExistFreshmail;
use App\Libraries\StatusCodeSubscriber;
use App\Libraries\Freshmail\SubscribersFreshmail;
use App\Libraries\IAI\GetClientIai;
use App\Libraries\IAI\IaiApi;
use App\Libraries\IAI\SetClientIai;
use Exception;

class FreshmailSubscriptionEmailHandler
{
    public $status;
    public $messageError;
    private $requestContent;
    private $mailingSystem;

    const UNSUBSCRIBEEVENT = "unsubscribe";
    const OPENEVENT = "open";
    const CLICKVENT = "click";



    public function __construct($requestContent)
    {
        $this->requestContent = $requestContent;
        $this->mailingSystem = new GetClientIai();
    }
    public function handle()
    {
        $shelledEvents = $this->shellEvents($this->requestContent);
        if (isset($shelledEvents[self::UNSUBSCRIBEEVENT])) {
            $falseServedEvents = $this->updateApprovals($shelledEvents[self::UNSUBSCRIBEEVENT]);
            return $this->createResponse($falseServedEvents);
        } else {
            return $this->createResponse();
        }
    }

    public function createResponse($falseServedEvents = [], $forceStatus = null)
    {
        $response = [];
        foreach ($this->requestContent as $event) {
            $responseForEvent = [
                'status'  => $forceStatus === null ? !in_array($event['hash'], $falseServedEvents) : $forceStatus,
                'hash'  => $event['hash']
            ];
            array_push($response, $responseForEvent);
        }

        return $response;
    }

    private function shellEvents($responsedEvents)
    {
        $events = [];
        foreach ($responsedEvents as $responseEvent) {
            if (!isset($events[$responseEvent['event']])) {
                $events[$responseEvent['event']] = [];
            }
            array_push($events[$responseEvent['event']], $responseEvent);
        }

        return $events;
    }

    public function updateApprovals($subscribers)
    {
        $falseServedEvents = [];
        foreach ($subscribers as $subscriber) {
            try {
                $clients = $this->mailingSystem->getClientsByEmail($subscriber['email']);
                $setClient = new SetClientIai();
                $setClient->updateApproval(StatusCodeSubscriber::REMOVED, $clients);
            } catch (ErrorApiIai $e) {
                $serveEvent['status'] = false;
                array_push($falseServedEvents, $subscriber['hash']);
            }
        }

        return $falseServedEvents;
    }
}
