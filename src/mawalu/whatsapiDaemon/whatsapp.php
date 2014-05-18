<?php

namespace mawalu\whatsapiDaemon;

use \WhatsApi\WhatsProtocol;

/**
 * A little wrapper for the whatsapi
 * @package mawalu\whatsapiDaemon
 */
class whatsapp
{

    /**
     * The whatsapi object
     * @var \WhatsApi\WhatsProtocol
     */
    private $wa;

    /**
     * Connect to whatsapp and initialize the event handler
     * @param $sender
     * @param $imei
     * @param $nickname
     * @param $password
     * @param $events
     */
    public function __construct($sender, $imei, $nickname, $password, $events)
    {
        $this->wa = new WhatsProtocol($sender, $imei, $nickname, FALSE);
        $this->wa->eventManager()->addEventListener($events);
        $this->wa->connect();
        $this->wa->loginWithPassword($password);
    }

    /**
     * Call a whatsapi function
     * @param $func
     * @param array $arg
     * @return mixed
     */
    public function callFunction($func, $arg = [])
    {
        return call_user_func_array(array($this->wa, $func), $arg);
    }

    /**
     * Start an endless loop and poll for new messages and events
     */
    public function poll()
    {
        while(true) {
            $this->wa->PollMessages();
            sleep(1);
        }
    }

    /**
     * Poll once for new message and events
     */
    public function pollOnce()
    {
        $this->wa->PollMessages();
    }

}
