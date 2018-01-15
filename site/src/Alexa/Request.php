<?php

namespace Alexa;

/**
 * Class Request
 * @package Alexa
 */
class Request
{
    private $request;

    /**
     * Request constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    /**
     * @param $request
     */
    private function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->request->type;
    }

    /**
     * @return mixed
     */
    public function getRequestID()
    {
        return $this->request->requestId;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->request->timestamp;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->request->locale;
    }

    /**
     * @return mixed
     */
    public function getIntent()
    {
        return $this->request->intent->name;
    }
}
