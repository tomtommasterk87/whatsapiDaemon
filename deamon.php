#!/usr/bin/php
<?php

require 'vendor/autoload.php';
require 'config/config.php';

$server = new mawalu\whatsapiDeamon\server("tcp://0.0.0.0:4444");
$events = new mawalu\whatsapiDeamon\events;
$whatsapp = new mawalu\whatsapiDeamon\whatsapp($sender, $imei, $nickname, $password, $events);

for(;;) {
    // Call socket() and pars all new messages recived from socket clients
    foreach ($server->socket() as $msg) {
        $data = json_decode($msg['data']);
        if ($data->action == 'addEvent') {
            $events->registerHandler($msg['from'], $data->event);
        }
    }

    // Poll for new whatsapp messages / events
    $whatsapp->pollOnce();
}
