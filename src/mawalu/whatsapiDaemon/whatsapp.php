<?php

namespace mawalu\whatsapiDaemon;

use Psr\Log\LoggerInterface;
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
     * Any Psr conform logger class
     * @var LoggerInterface
     */
    private $log;

    /**
     * Connect to whatsapp and initialize the event handler
     *
     * @param \WhatsApi\WhatsProtocol $whatsapp
     * @param \mawalu\whatsapiDaemon\events $events
     * @param string $password
     * @param LoggerInterface $log
     */
    public function __construct(WhatsProtocol $whatsapp,  events $events, $password, LoggerInterface $log)
    {
        $this->log = $log;
        $this->wa = $whatsapp;
        $this->wa->eventManager()->addEventListener($events);
        $this->wa->connect();
        $this->wa->loginWithPassword($password);
        $this->log->info("Connected to whatsapp");
    }

    /**
     * Call a whatsapi function
     *
     * @param $func
     * @param array $arg
     * @return mixed
     */
    public function callFunction($func, $arg = [])
    {
        if(method_exists($this->wa, $func)) {
            $this->log->info("Calling whatsapi function", array($func, $arg));
            return call_user_func_array(array($this->wa, $func), $arg);
        }
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
