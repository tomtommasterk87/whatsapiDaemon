#!/usr/bin/php
<?php

require 'vendor/autoload.php';
require 'config/config.php';

use mawalu\whatsapiDaemon\whatsapp;
use mawalu\whatsapiDaemon\server;
use mawalu\whatsapiDaemon\events;
use WhatsApi\WhatsProtocol;

$server = new server($stream);
$events = new events;
$whatsapp = new whatsapp(new WhatsProtocol($sender, $imei, $nickname, FALSE), $events, $password);

for(;;) {
    // Call socket() and pars all new messages received from socket clients
    foreach ($server->socket($events->getTodo()) as $msg) {
        $data = json_decode($msg['data']);
        if ($data !== null && $data->action === 'addEvent') {
            $events->registerHandler($msg['from'], $data->args);
        }elseif($data !== null) {
            $whatsapp->callFunction($data->action, $data->args);
        }
    }
    $events->doneTodo();

    // Poll for new whatsapp messages / events
    $whatsapp->pollOnce();
}
