<?php

use App\Libraries\Freshmail\FreshmailApi;
use App\Libraries\StatusCodeSubscriber;
use App\Libraries\Freshmail\SubscriberListFreshmail;
use App\Libraries\Freshmail\SubscribersFreshmail;
use App\Libraries\Freshmail\Exceptions\EmailAddresIsIncorrect;
use App\Libraries\IAI\GetClientIai;
use App\Libraries\IAI\SetClientIai;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['inspect'])->prefix('webhook')->group(function () {
    Route::post('/iai', 'IaiController@catchWebhook')->middleware('auth.webhook.iai');
    Route::any('/freshmail', 'FreshmailController@catchWebhook');
});

Route::get('catch-subscription-approval{redirectlink}', "IaiController@catchApprovalSubscription");
