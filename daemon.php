#!/usr/bin/php
<?php

require 'vendor/autoload.php';
require 'config/config.php';

use Monolog\Logger;
use WhatsApi\WhatsProtocol;
use mawalu\whatsapiDaemon\server;
use mawalu\whatsapiDaemon\events;
use Monolog\Handler\RotatingFileHandler;
use mawalu\whatsapiDaemon\whatsapp;

$log = new Logger($nickname);
$log->pushHandler(new RotatingFileHandler(__DIR__ . '/log/debug', 0, Logger::DEBUG));
$log->info("Started");

$server = new server($stream, $log);
$events = new events($log);
$whatsapp = new whatsapp(new WhatsProtocol($sender, $imei, $nickname, FALSE), $events, $password, $log);

for(;;) {
    // Call socket() and pars all new messages received from socket clients
    foreach ($server->socket($events->getTodo()) as $msg) {
        $data = json_decode($msg['data']);
        if($data !== null) {
            $log->info("Received socket command", array($data->action, $data->args));

            switch($data->action) {
                case 'registerHandler':
                    $events->registerHandler($msg['from'], $data->args);
                    break;
                case 'removeHandler':
                    $events->removeHandler($msg['from'], $data->args);
                    break;
                case 'removeAllHandler':
                    $events->removeAllHandlers($msg['from']);
                    break;
                default:
                    $whatsapp->callFunction($data->action, $data->args);
            }
        }
    }
    $events->doneTodo();

    // Poll for new whatsapp messages / events
    $whatsapp->pollOnce();
}
