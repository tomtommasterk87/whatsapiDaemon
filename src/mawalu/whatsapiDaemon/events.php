<?php

namespace mawalu\whatsapiDaemon;

use Psr\Log\LoggerInterface;
use \WhatsApi\Events\WhatsAppEventListenerProxy;

/**
 * Handles all events from whatsapi and the socket api
 * @package mawalu\whatsapiDaemon
 */
class events extends WhatsAppEventListenerProxy
{
    /**
     * Save all handlers, registered using the socket API
     * @var array
     */
    private $handler = array();
    /**
     * Save everything we need to send to our clients
     * @var array
     */
    private $todo = array();
    /**
     * Any Psr conform logger class
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * This gets called by the whatsapi whe something happens
     * @param string $eventName
     * @param array $arguments
     * @return null
     */
    public function handleEvent($eventName, array $arguments)
    {
        $this->log->info("Event got triggered", array($eventName, $arguments));
        foreach ($this->searchForHandler($eventName) as $val) {
            if(isset($this->todo[$val])) {
                $this->todo[$val][] = array($eventName => $arguments);
            } else {
                $this->todo[$val] = array(array($eventName => $arguments));
            }
        }
    }

    /**
     * Get all the handlers registered for an event
     * @param $event
     * @return array
     */
    public function searchForHandler($event) {
        $return = array();
        foreach ($this->handler as $key => $val) {
            if (in_array($event, $val)) {
                $return[] = $key;
            }
        }
        return $return;
    }

    /**
     * Register an handler for an event
     *
     * @param $from
     * @param $events
     */
    public function registerHandler($from, $events)
    {
        if(!isset($this->handler[$from])) {
            $this->handler[$from] = array();
        }

        foreach($events as $event) {
            $this->log->info("Registered event handler", array($from, $event));
            $this->handler[$from][] = $event;
        }
    }

    /**
     * Remove an event handler
     *
     * @param $from
     * @param $events
     */
    public function removeHandler($from, $events)
    {
        foreach($events as $event) {
            $this->log->info("Removed event handler", array($from, $event));
            unset($this->handler[$from][array_search($event, $this->handler[$from])]);
        }
    }

    /**
     * Remove all event handlers form one, or all clients
     * @param null $from
     */
    public function removeAllHandlers($from = null)
    {
        if($from == null) {
            $this->log->info("Removed all event handlers");
            $this->handler = array();
        } else {
            if (isset($this->handler[$from])) {
                $this->log->info("Removed all event handlers from", array($from));
                unset($this->handler[$from]);
            }
        }
    }

    /**
     * Get all events we need to sent to our socket clients
     * @return array
     */
    public function getTodo()
    {
        return $this->todo;
    }

    /**
     * Empty the list of items to sent
     */
    public function doneTodo()
    {
        $this->todo = array();
    }
}