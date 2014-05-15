<?php

namespace mawalu\whatsapiDeamon;

use \WhatsApi\WhatsProtocol;

class whatsapp
{

    private $wa;

    public function __construct($sender, $imei, $nickname, $password, $events)
    {
        $this->wa = new WhatsProtocol($sender, $imei, $nickname, FALSE);
        $this->wa->eventManager()->addEventListener($events);
        $this->wa->connect();
        $this->wa->loginWithPassword($password);
    }

    public function callFunction($func, $arg = [])
    {
        return call_user_func_array(array($this->wa, $func), $arg);
    }

    public function poll()
    {
        while(true) {
            $this->wa->PollMessages();
            sleep(1);
        }
    }

    public function pollOnce()
    {
        $this->wa->PollMessages();
    }

}
