<?php

namespace mawalu\whatsapiDeamon;

class deamonEvents extends \WhatsApi\Events\WhatsAppEventListenerBase
{
    function onGetMessage(
      $phone,
      $from,
      $msgid,
      $type,
      $time,
      $name,
      $message
    )
    {
    	echo $message;
    }
}