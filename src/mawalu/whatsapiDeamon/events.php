<?php

namespace mawalu\whatsapiDeamon;

use \WhatsApi\Events\WhatsAppEventListenerProxy;

class events extends WhatsAppEventListenerProxy
{
    private $handler = array();
    private $todo = array();

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

    private function searchForHandler($handler) {
        $return = array();
        foreach ($this->handler as $key => $val) {
            if (in_array($handler, $val)) {
                $return[] = $key;
            }
        }
        return $return;
    }

    public function registerHandler($from, $name)
    {
        if(isset($this->handler[$from])) {
            $this->handler[$from][] = $name;
        } else {
            $this->handler[$from] = array($name);
        }
    }

    public function removeHandler($from, $name)
    {
        unset($this->handler[$from][$name]);
    }
    
    public function getTodo()
    {
        return $this->todo;
    }
    
    public function doneTodo()
    {
        $this->todo = array();
    }
}