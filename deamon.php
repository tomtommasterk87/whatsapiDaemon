#!/usr/bin/php
<?php

require 'vendor/autoload.php';
require 'config/config.php';

$server = new mawalu\whatsapiDeamon\server("tcp://0.0.0.0:4444");
$events = new mawalu\whatsapiDeamon\events;
$whatsapp = new mawalu\whatsapiDeamon\whatsapp($sender,
                                               $imei,
                                               $nickname,
                                               $password,
                                               $events
                                              );

for(;;) {
    // Call socket() and pars all new messages received from socket clients
    foreach ($server->socket($events->getTodo()) as $msg) {
        $data = json_decode($msg['data']);
        if ($data !== null && $data->action === 'addEvent') {
            $events->registerHandler($msg['from'], $data->event);
        }elseif($data !== null) {
            $whatsapp->callFunction($data->action, $data->args);
        }
    }
    $events->doneTodo();

    // Poll for new whatsapp messages / events
    $whatsapp->pollOnce();
}
