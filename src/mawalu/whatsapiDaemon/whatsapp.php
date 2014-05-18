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
     *
     * @param \WhatsApi\WhatsProtocol $whatsapp
     * @param \mawalu\whatsapiDaemon\events $events
     * @param $password
     *
     * @internal param $sender
     * @internal param $imei
     * @internal param $nickname
     */
    public function __construct(WhatsProtocol $whatsapp,  events $events, $password)
    {
        $this->wa = $whatsapp;
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
