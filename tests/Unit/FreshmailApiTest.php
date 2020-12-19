<?php

namespace Tests\Unit;

use App\Libraries\Freshmail\FreshmailApi;
use App\Libraries\StatusCodeSubscriber;
use App\Libraries\Freshmail\SubscriberListFreshmail;
use App\Libraries\Freshmail\SubscribersFreshmail;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class FreshmailApiTest extends TestCase
{
    use CreatesApplication;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function statusIsOk($response)
    {
        $this->assertEquals('OK', $response['status']);
    }

    public function testPingPost()
    {
        $freshmailApi = new FreshmailApi();
        $response = $freshmailApi->pingPOST();

        $this->statusIsOk($response);
    }

    public function testPingGet()
    {
        $freshmailApi = new FreshmailApi();
        $response = $freshmailApi->pingGET();

        $this->statusIsOk($response);
        $this->assertEquals('pong', $response['data']);
    }

    public function testGetHashList()
    {
        $test = new SubscriberListFreshmail();
        $response = $test->getHashList('Odbiorcy PL');

        $this->assertEquals('cmxbcjnacw', $response);
    }

    public function testGetSubscriber()
    {
        $test = new SubscribersFreshmail();
        $response = $test->get('Odbiorcy PL', "test@phu-szczepan.pl");

        $this->statusIsOk($response);
        $this->assertEquals('test@phu-szczepan.pl', $response['data']['email']);
    }

    public function testGetAndUpdateApproval()
    {
        $test = new SubscribersFreshmail;
        $test->updateApproval(StatusCodeSubscriber::REMOVED, 'test@phu-szczepan.pl', 'Odbiorcy PL');

        $this->assertTrue($test->hasApproval('Odbiorcy PL', 'test@phu-szczepan.pl') == StatusCodeSubscriber::REMOVED);
    }

    public function testSubscriberDoesntExistHasApproval()
    {
        $test = new SubscribersFreshmail;
        $this->assertIsInt($test->hasApproval('Odbiorcy PL', 'test111@phu-szczepan.pl'));
    }
}
