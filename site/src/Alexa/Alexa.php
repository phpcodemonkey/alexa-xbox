<?php

namespace Alexa;

use Monolog\Logger;

/**
 * Class Alexa
 * @package Alexa
 */
class Alexa
{
    private $input;
    private $session;
    private $request;

    private $applicationId = '';
    private $applicationName = '';
    private $card = '';
    private $rePrompt = '';
    private $outputSpeech = '';
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Alexa constructor.
     */
    public function __construct()
    {
        $this->getInput();
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    /**
     *
     */
    private function getInput()
    {
        $this->input = json_decode(file_get_contents('php://input'));

        if (isset($this->input->session)) {
            $this->session = new Session($this->input->session);
        }

        if (isset($this->input->request)) {
            $this->request = new Request($this->input->request);
        }
    }

    /**
     * @param $applicationId
     */
    public function setApplicationID($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return bool
     */
    public function auth()
    {
        return $this->input !== '' && $this->getSession() &&
            $this->applicationId === $this->getSession()->getApplicationID();
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->input->version;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $applicationName
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
    }

    /**
     * @param $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * @param $rePrompt
     */
    public function setReprompt($rePrompt)
    {
        $this->rePrompt = $rePrompt;
    }

    /**
     * @param $outputSpeech
     */
    public function setOutputSpeech($outputSpeech)
    {
        $this->outputSpeech = $outputSpeech;
    }

    /**
     *
     */
    public function displayOutput()
    {
        $output = <<< EOF
{
    "version": "1.0",
    "response": {
        "outputSpeech": {
            "type": "PlainText",
            "text": "{$this->outputSpeech}"
        },
        "card": {
            "type": "Simple",
            "title": "{$this->applicationName}",
            "content": "{$this->card}"
        },
        "reprompt": {
            "outputSpeech": {
                "type": "PlainText",
                "text": "{$this->rePrompt}"
            }
        },
        "shouldEndSession": true
    }
}
EOF;

        if ($this->logger) {
            $this->logger->debug(__METHOD__ . ' Response', ['output' => $output]);
        }

        echo $output;
    }
}
