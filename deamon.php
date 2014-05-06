#!/usr/bin/php
<?php

require 'vendor/autoload.php';
require 'config/config.php';

$wa = new WhatsApi\WhatsProtocol($sender, $imei, $nickname, FALSE);
$wa->eventManager()->addEventListener(new mawalu\whatsapiDeamon\deamonEvents);
$wa->connect();
$wa->loginWithPassword($password);

while (TRUE) {
	$wa->PollMessages();
	sleep(1);
}
