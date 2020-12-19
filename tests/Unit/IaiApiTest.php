<?php

namespace Tests\Unit;

use App\Libraries\IAI\GetClientIai;
use App\Libraries\IAI\SetClientIai;
use App\Libraries\StatusCodeSubscriber;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class IaiApiTest extends TestCase
{
    use CreatesApplication;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetClientById()
    {
        $clientId = "17786";
        $client = new GetClientIai();
        $client = $client->getClientById($clientId);

        $this->assertTrue($client['client_number'] == $clientId);
    }

    public function testHasApproval()
    {
        $client = new GetClientIai();
        $approval = $client->hasApproval(17786, "test@phu-szczepan.pl");
        $this->assertIsInt($approval);
    }

    public function testOneClientUpdateApprovalAndNotApproval()
    {
        $this->oneClientUpdateApproval();
        $this->oneClientUpdateApproval();
    }

    public function oneClientUpdateApproval()
    {

        $clientGet = new GetClientIai();
        $client = $clientGet->getClientById(17786);
        $approvalBeforeUpdate = $clientGet->hasApproval(17786, 'test@phu-szczepan.pl');

        $newApprovalUpdate = $approvalBeforeUpdate == StatusCodeSubscriber::REMOVED ?
            StatusCodeSubscriber::ACTIVE :
            StatusCodeSubscriber::REMOVED;

        $setClient = new SetClientIai();
        $response = $setClient->updateApproval($newApprovalUpdate, [$client]);

        $approvalAfterUpdate = $clientGet->hasApproval(17786, 'test@phu-szczepan.pl');

        $controlArray = [
            [
                "status" => true,
                "params" => [
                    "id" => "17786"
                ]
            ]
        ];

        $this->assertTrue($approvalAfterUpdate ==  $newApprovalUpdate);
        $this->assertEquals($controlArray, $response['clients']);
    }
}
