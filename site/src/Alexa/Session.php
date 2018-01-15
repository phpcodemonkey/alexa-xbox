<?php

namespace Alexa;

/**
 * Class Session
 * @package Alexa
 */
class Session
{
    private $session;

    /**
     * Session constructor.
     * @param $session
     */
    public function __construct($session)
    {
        $this->setSession($session);
    }

    /**
     * @param $session
     */
    private function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function isNewSession()
    {
        return $this->session->new;
    }

    /**
     * @return mixed
     */
    public function getSessionID()
    {
        return $this->session->sessionId;
    }

    /**
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->session->application->applicationId;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->session->user->userId;
    }

    /**
     * @return mixed
     */
    public function getUserAccessToken()
    {
        return $this->session->user->accessToken;
    }
}
