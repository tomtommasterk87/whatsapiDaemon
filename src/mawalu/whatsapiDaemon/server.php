<?php

namespace mawalu\whatsapiDaemon;

use Psr\Log\LoggerInterface;

/**
 * Class server
 * This class handel's all socket clients
 *
 * @package mawalu\whatsapiDaemon
 */
class server
{
    /**
     * Socket server
     * @var resource
     */
    private $server;
    /**
     * All the clients
     * @var array
     */
    private $clientSocks;
    /**
     * Everything that needs to be send
     * @var array
     */
    private $toSent;
    /**
     * Any Psr conform logger class
     * @var LoggerInterface
     */
    private $log;

    /**
     * Create the listening socket
     * @param $stream
     * @param LoggerInterface $log
     * @throws \Exception
     */
    public function __construct($stream, LoggerInterface $log)
    {
        $this->log = $log;
        $this->server = stream_socket_server($stream, $errno, $errorMessage);
        stream_set_blocking( $this->server, 0 );
        $this->client_socks = array();

        if ($this->server === false) {
            throw new \Exception("Could not bind socket to port");
        }
    }

    /**
     * Send data as chunks
     * @param $data
     * @param $sock
     */
    public function sendChunk($data, $sock) {
        $this->log->info("Sending data to socket client", array($sock, $data));
        $data = chunk_split($data, 128);
        if(is_array($data)) {
            foreach ($data as $send) {
                fwrite($sock, $send);
            }
        } else {
            fwrite($sock, $data);
        }

    }

    /**
     * Poll for messages, client and send new events
     * @param $toSent
     * @return array
     */
    public function socket($toSent)
    {
        $readSocks = $this->clientSocks;
        $readSocks[] = $this->server;
        $this->toSent = $toSent;
        $return = array();

        foreach($readSocks as $sock) {
            $name = stream_socket_get_name($sock, true);
            if(isset($this->toSent[$name])) {
                foreach ($this->toSent[$name] as $event) {
                    $this->sendChunk(json_encode($event), $sock);
                }
            }
        }

        //start reading
        if(stream_select ( $readSocks, $write, $except, 1 )) {
            //new client
            if(in_array($this->server, $readSocks)) {
                $newClient = stream_socket_accept($this->server);
                         
                if ($newClient) {
                    //print remote client information, ip and port number
                    $this->log->info("Connection accepted", array(stream_socket_get_name($newClient, true)));

                    $this->clientSocks[] = $newClient;
                }
                         
                //delete the server socket from the read sockets
                unset($readSocks[ array_search($this->server, $readSocks) ]);
            }
                     
            //message from existing client
            foreach($readSocks as $sock) {
                $name = stream_socket_get_name($sock, true);

                $data[] = fread($sock, 128);
                $data = join("", $data);

                if($data == "") {
                    unset($this->clientSocks[ array_search($sock, $this->clientSocks) ]);
                    $this->log->info("Client disconnected", array($name));
                } else {
                    $return[] = array('from' => $name, 'data' => $data);
                    $this->log->info("Received data from client", array($name, $data));
                }
            }
        }

        return $return;
    }
}