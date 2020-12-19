<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FreshmailSubscriptionEmailHandlerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFreshmailSubscriptionEmailHandlerWithAllEvents()
    {
        $bodyContent = [
            0 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'john.doe@freshmail.com',
                'event' => 'open',
                'timestamp' => 1439889386,
                'attempt' => 1,
                'hash' => '1',
            ],
            1 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'test@phu-szczepan.pl',
                'event' => 'open',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '2',
            ],
            2 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'test@phu-szczepan.pl',
                'event' => 'open',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '3',
            ],
            3 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'k.domanczyk@rillfit.pl!',
                'event' => 'unsubscribe',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '4',
            ],
            4 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'k.domanczyk@rillfit.pl!',
                'event' => 'unsubscribe',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '4',
            ],
        ];

        $response = $this->postJson('webhook/freshmail', $bodyContent);


        $responsCorrect = array(
            0 =>
            array(
                'status' => true,
                'hash' => '1',
            ),
            1 =>
            array(
                'status' => true,
                'hash' => '2',
            ),
            2 =>
            array(
                'status' => true,
                'hash' => '3',
            ),
            3 =>
            array(
                'status' => false,
                'hash' => '4',
            ),
            4 =>
            array(
                'status' => false,
                'hash' => '4',
            ),
        );

        $response->assertJson($responsCorrect);
    }
    public function testFreshmailSubscriptionEmailHandlerWithoutUnsubscribeEvents()
    {
        $bodyContent = [
            0 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'john.doe@freshmail.com',
                'event' => 'open',
                'timestamp' => 1439889386,
                'attempt' => 1,
                'hash' => '1',
            ],
            1 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'test@phu-szczepan.pl',
                'event' => 'open',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '2',
            ],
            2 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'test@phu-szczepan.pl',
                'event' => 'open',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '3',
            ],
            3 => [
                'campaign' => 'nsqqmsb52q',
                'email' => 'k.domanczyk@rillfit.pl!',
                'event' => 'open',
                'timestamp' => 1439889404,
                'attempt' => 1,
                'hash' => '4',
            ],

        ];

        $response = $this->postJson('webhook/freshmail', $bodyContent);


        $responsCorrect = array(
            0 =>
            array(
                'status' => true,
                'hash' => '1',
            ),
            1 =>
            array(
                'status' => true,
                'hash' => '2',
            ),
            2 =>
            array(
                'status' => true,
                'hash' => '3',
            ),
            3 =>
            array(
                'status' => true,
                'hash' => '4',
            ),
        );

        $response->assertJson($responsCorrect);
    }
}
