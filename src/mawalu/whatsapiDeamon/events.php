<?php

namespace mawalu\whatsapiDeamon;

class events extends \WhatsApi\Events\WhatsAppEventListenerProxy
{
    private $handler = array();

    protected function handleEvent($eventName, array $arguments)
    {
        $this->searchForHandler($eventName);
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
        
    }
}