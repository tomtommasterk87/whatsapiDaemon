<?php

namespace mawalu\whatsapiDaemon;

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
     * This gets called by the whatsapi whe something happens
     * @param string $eventName
     * @param array $arguments
     * @return null
     */
    protected function handleEvent($eventName, array $arguments)
    {
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
    private function searchForHandler($event) {
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
     * @param $from
     * @param $name
     */
    public function registerHandler($from, $name)
    {
        if(isset($this->handler[$from])) {
            $this->handler[$from][] = $name;
        } else {
            $this->handler[$from] = array($name);
        }
    }

    /**
     * Remove an event handler
     * @param $from
     * @param $name
     */
    public function removeHandler($from, $name)
    {
        unset($this->handler[$from][$name]);
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