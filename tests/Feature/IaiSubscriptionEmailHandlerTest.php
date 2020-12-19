<?php

namespace Tests\Feature;

use App\Libraries\StatusCodeSubscriber;
use App\Libraries\Freshmail\SubscribersFreshmail;
use App\Libraries\IAI\GetClientIai;
use App\Libraries\IAI\SetClientIai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IaiSubscriptionEmailHandlerTest extends TestCase
{

    public function getHeaders()
    {
        return array(
            'content-type' =>
            array(
                0 => 'application/json',
            ),
            'accept' =>
            array(
                0 => '*/*',
            ),
            'x-webhook-hash' =>
            array(
                0 => 'a5927bc7b9b0d071c680028d541dfd71f280c4951c5019f116427a8425374632',
            ),
            'x-webhook-campaign-id' =>
            array(
                0 => '7',
            ),
            'x-webhook-event-id' =>
            array(
                0 => '17',
            ),
            'x-webhook-id' =>
            array(
                0 => '3',
            ),
        );
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCatchWebhookCorrectlyAndAuthorized()
    {
        $this->withExceptionHandling();


        $response = $this->withHeaders($this->getHeaders())->postJson('webhook/iai', [
            'email' => 'test@phu-szczepan.pl',
            'id' => 17786,
        ]);


        $response->assertStatus(200)->assertJson([
            'status' => 'correct',
        ]);
    }

    public function testCatchWebhookCorrectlyAndUnAuthorized()
    {
        $this->withExceptionHandling();

        $headers = $this->getHeaders();
        $headers['x-webhook-id'] = 1;
        $response = $this->withHeaders($headers)->postJson('webhook/iai', [
            'email' => 'test@phu-szczepan.pl',
            'id' => 17786,
        ]);


        $response->assertStatus(401)->assertJson([
            'status' => 'error',
        ]);
    }


    public function testCatchWithDiffrentEmailForClient()
    {

        $response = $this->withHeaders($this->getHeaders())->postJson('webhook/iai', [
            'email' => 'test@phu-szczepan.pl!!!',
            'id' => 17786,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Niepoprawny adres e-mail: test@phu-szczepan.pl!!! u klienta o id 17786'
            ]);
    }

    public function testCatchWebhookUpdatedWhereIaiActiveFreshmailRemoved()
    {
        $this->withExceptionHandling();


        $freshmail = new SubscribersFreshmail;
        $freshmail->updateApproval(StatusCodeSubscriber::REMOVED, 'test@phu-szczepan.pl', 'Odbiorcy PL');

        $iai = new GetClientIai();
        $iaiApproval = $iai->hasApproval(17786, 'test@phu-szczepan.pl');


        $response = $this->withHeaders($this->getHeaders())->postJson('webhook/iai', [
            'email' => 'test@phu-szczepan.pl',
            'id' => 17786,
        ]);

        $updatedFreshmailApproval = $freshmail->hasApproval('Odbiorcy PL', 'test@phu-szczepan.pl');

        $this->assertTrue($updatedFreshmailApproval == $iaiApproval);
        $response->assertStatus(200)->assertJson([
            'status' => 'correct',
        ]);
    }

    public function testCatchWebhookUpdatedWhereIaiActiveFreshmailActive()
    {
        $this->withExceptionHandling();


        $freshmail = new SubscribersFreshmail;
        $freshmail->updateApproval(StatusCodeSubscriber::ACTIVE, 'test@phu-szczepan.pl', 'Odbiorcy PL');

        $iai = new GetClientIai();
        $client = $iai->getClientsByEmail('test@phu-szczepan.pl');
        $iaiSetClient = new SetClientIai();
        $iaiSetClient->updateApproval(StatusCodeSubscriber::ACTIVE, $client);

        $iaiApproval = $iai->hasApproval(17786, 'test@phu-szczepan.pl');


        $response = $this->withHeaders($this->getHeaders())->postJson('webhook/iai', [
            'email' => 'test@phu-szczepan.pl',
            'id' => 17786,
        ]);

        $updatedFreshmailApproval = $freshmail->hasApproval('Odbiorcy PL', 'test@phu-szczepan.pl');

        $this->assertTrue($updatedFreshmailApproval == $iaiApproval);
        $response->assertStatus(200)->assertJson([
            'status' => 'correct',
            'message' => [
                'updated' => false,
                "email" => "test@phu-szczepan.pl",
                "approvalIai" => 1,
                "approvalMailingSystem" => 1
            ]
        ]);
    }
}
