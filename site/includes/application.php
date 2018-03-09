<?php

use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (is_readable(__DIR__ . DIRECTORY_SEPARATOR . '.env')) {
    $dotEnv = new Dotenv(__DIR__);
    $dotEnv->load();
}

if (!array_key_exists('MONOLOG_NAME', $_ENV) || !array_key_exists('MONOLOG_FILE', $_ENV)) {
    throw new InvalidArgumentException('Missing Monolog variables');
}

// Set up Monolog
$logger = new Logger($_ENV['MONOLOG_NAME']);

$formatter = new LineFormatter(
    isset($_ENV['MONOLOG_LINE_FORMATTER']) ? $_ENV['MONOLOG_LINE_FORMATTER'] . PHP_EOL : null
);

// Set up the Monolog handlers
$rotating = new RotatingFileHandler(
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $_ENV['MONOLOG_FILE'],
    $_ENV['MONOLOG_MAX_FILES']
);
$rotating->setFormatter($formatter);

$handlers = [$rotating];

if (array_key_exists('SLACK_API_TOKEN', $_ENV) &&
    array_key_exists('SLACK_CHANNEL', $_ENV) &&
    array_key_exists('SLACK_USER', $_ENV) &&
    !empty($_ENV['SLACK_API_TOKEN']) &&
    !empty($_ENV['SLACK_CHANNEL']) &&
    !empty($_ENV['SLACK_USER'])
) {
    $slack_log_level = Logger::DEBUG;
    $levels = Logger::getLevels();
    if (array_key_exists('SLACK_LOG_LEVEL', $_ENV) && array_key_exists($_ENV['SLACK_LOG_LEVEL'], $levels)) {
        $slack_log_level = $levels[$_ENV['SLACK_LOG_LEVEL']];
    }

    $handlers[] =
        // Set up the slack log handler
        new SlackHandler(
            $_ENV['SLACK_API_TOKEN'], // Loaded from .env file
            $_ENV['SLACK_CHANNEL'], // Provide your own channel or private channel
            $_ENV['SLACK_USER'],
            true,
            null,
            $slack_log_level, // Change the log level to vary chattiness of output
            true,
            true,
            true
        );
    $_ENV['SLACK_API_TOKEN'] = $_SERVER['SLACK_API_TOKEN'] = '*** Protected ***';
    putenv('SLACK_API_TOKEN="*** Protected ***"');
}

$logger->setHandlers($handlers);

// Set up Whoops
$whoops = new Run();

// Add the default Whoops handlers
$whoops->pushHandler(new JsonResponseHandler());
$whoops->pushHandler(new PrettyPageHandler());

// Set up the monolog custom handler
$whoops->pushHandler(
    function ($exception, $inspector, $run) use ($logger) {
        /* @var $exception \Exception */
        $logger->log(
            Logger::ERROR,
            $logger->getName() . ': ' . $exception->getMessage(),
            [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );
    }
);

$whoops->register();
