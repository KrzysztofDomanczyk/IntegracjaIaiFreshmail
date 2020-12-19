<?php

namespace App\Libraries\Freshmail;

use App\Libraries\Freshmail\Exceptions\NotFoundHashListOrList;

class SubscriberListFreshmail extends FreshmailApi
{
    public function getHashList($name)
    {
        $this->address = '/rest/subscribers_list/lists';

        $this->sendGet();

        return $this->extractHashFromResponse($name);
    }

    private function extractHashFromResponse($name)
    {
        $hash = null;
        foreach ($this->response['lists'] as $list) {
            if ($list['name'] == $name) {
                $hash = $list['subscriberListHash'];
            };
        }

        if ($hash == null) {
            throw new NotFoundHashListOrList();
        }


        return $hash;
    }
}
