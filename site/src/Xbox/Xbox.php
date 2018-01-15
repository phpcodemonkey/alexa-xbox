<?php

namespace Xbox;

use Monolog\Logger;

/**
 * Class Xbox
 * @package Xbox
 */
class Xbox
{
    private $pingVal = "dd00000a000000000000000400000002";
    private $port = 5050;
    private $ipAddress = '';
    private $xboxLiveId = '';
    private $retries = 3;

    /**
     * @var Logger
     */
    private $logger;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @param $xboxLiveId
     */
    public function setXboxLiveId($xboxLiveId)
    {
        $this->xboxLiveId = $xboxLiveId;
    }

    /**
     * @param $retries
     */
    public function setRetryCount($retries)
    {
        $this->retries = $retries;
    }

    /**
     * @return resource
     */
    private function getSocket()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        socket_set_block($socket);
        socket_connect($socket, $this->ipAddress, $this->port);

        return $socket;
    }

    /**
     * @param $hex
     * @return string
     */
    private function hex2str($hex)
    {
        $str = '';
        $len = strlen($hex);

        for ($i = 0; $i < $len; $i += 2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }

        return $str;
    }

    /**
     * @return bool
     */
    private function sendOn()
    {
        $socket = $this->getSocket();

        $data = "\x00" . chr(strlen($this->xboxLiveId)) . $this->xboxLiveId . "\x00";
        $header = "\xdd\x02\x00" . chr(strlen($data)) . "\x00\x00";

        $status = socket_send($socket, $header . $data, strlen($header . $data), MSG_EOR);

        sleep(1);

        if ($this->logger) {
            $this->logger->debug(__METHOD__ . ' Wake Packet', ['header' => $header, 'data' => $data]);
            $this->logger->debug(__METHOD__ . ' Status', ['length' => $status]);
        }

        if ($status) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function switchOn()
    {
        for ($i = 0; $i < $this->retries; $i++) {
            if ($this->sendOn() && $this->ping()) {
                return true;
            }
        }

        sleep(1);

        // One last check!
        return $this->ping();
    }

    /**
     * @return bool
     */
    public function ping()
    {
        $socket = $this->getSocket();

        $data = $this->hex2str($this->pingVal);

        socket_write($socket, $data, strlen($data));
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1, 'usec' => 0));

        $buffer = '';

        $bytes = socket_recv($socket, $buffer, 2048, MSG_WAITALL);

        if ($this->logger) {
            $this->logger->debug(__METHOD__ . ' Sent', ['data' => $data]);
            $this->logger->debug(__METHOD__ . ' Received', ['bytes' => $bytes, 'buffer' => $buffer]);
        }

        return $bytes && ($buffer !== null);
    }
}
