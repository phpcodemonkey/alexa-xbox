<?php

use Alexa\Alexa;
use Xbox\Xbox;

include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'application.php';

$alexa = new Alexa();
$xbox = new Xbox();

// Debug
if ($_ENV['DEBUG']) {
    $alexa->setLogger($logger);
    $xbox->setLogger($logger);
}

// Set alexa app specific data
$alexa->setApplicationID($_ENV['APP_ID']);  // Set the application ID for your skill here
$alexa->setApplicationName($_ENV['APP_NAME']);  // Change this to whatever you are calling your app

// Set Xbox IP address and live ID
$xbox->setIpAddress($_ENV['IP_ADDRESS']);  // Set the public IP address of your Xbox here
$xbox->setPort($_ENV['PORT']);
$xbox->setXboxLiveId($_ENV['XBOX_LIVE_ID']);  // Set the Xbox live ID here
$xbox->setRetryCount($_ENV['XBOX_WOL_RETRIES']);

// Authenticate request and execute
if ($_ENV['DEBUG'] || $auth = $alexa->auth()) {
    if (!empty($auth)) {
        $logger->debug('Authenticated');
    }
    if ($xbox->ping()) {
        $logger->info('Xbox already on');
        $alexa->setCard('Xbox is already on.');
        $alexa->setReprompt('');
        $alexa->setOutputSpeech('Your Xbox has already been turned on.');
    } else {
        if ($xbox->switchOn()) {
            $logger->info('Xbox turned on');
            $alexa->setCard('Xbox is now on.');
            $alexa->setReprompt('');
            $alexa->setOutputSpeech('Your Xbox is now turned on.');
        } else {
            $logger->info('Xbox not turned on');
            $alexa->setCard('Xbox couldn\'t be turned on.');
            $alexa->setReprompt('');
            $alexa->setOutputSpeech('Your Xbox could not be turned on. Please try again.');
        }
    }

    $alexa->displayOutput();
}
