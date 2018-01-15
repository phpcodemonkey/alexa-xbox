<?php

use Alexa\Alexa;
use Xbox\Xbox;

include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'application.php';

$alexa = new Alexa();
$xbox = new Xbox();

// Set alexa app specific data
$alexa->setApplicationID($_ENV['APP_ID']);  // Set the application ID for your skill here
$alexa->setApplicationName($_ENV['APP_NAME']);  // Change this to whatever you are calling your app

// Set Xbox IP address and live ID
$xbox->setIpAddress($_ENV['IP_ADDRESS']);  // Set the public IP address of your Xbox here
$xbox->setPort($_ENV['PORT']);
$xbox->setXboxLiveId($_ENV['XBOX_LIVE_ID']);  // Set the Xbox live ID here
$xbox->setRetryCount($_ENV['XBOX_WOL_RETRIES']);

// Authenticate request and execute
if ($alexa->auth()) {
    if ($xbox->ping()) {
        $alexa->setCard('Xbox is already on.');
        $alexa->setReprompt('');
        $alexa->setOutputSpeech('Your Xbox has already been turned on.');
    } else {
        if ($xbox->switchOn()) {
            $alexa->setCard('Xbox is now on.');
            $alexa->setReprompt('');
            $alexa->setOutputSpeech('Your Xbox is now turned on.');
        } else {
            $alexa->setCard('Xbox couldn\'t be turned on.');
            $alexa->setReprompt('');
            $alexa->setOutputSpeech('Your Xbox could not be turned on. Please try again.');
        }
    }

    $alexa->displayOutput();
}
