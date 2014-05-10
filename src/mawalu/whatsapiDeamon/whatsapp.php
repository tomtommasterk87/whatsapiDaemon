<?php

namespace mawalu\whatsapiDeamon;

class whatsapp
{

    private $wa;

    public function __construct($sender, $imei, $nickname, $password, $events)
    {
        $this->wa = new \WhatsApi\WhatsProtocol($sender, $imei, $nickname, FALSE);
        $this->wa->eventManager()->addEventListener($events);
        $this->wa->connect();
        $this->wa->loginWithPassword($password);
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