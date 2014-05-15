<?php

namespace mawalu\whatsapiDeamon;

class server
{
    private $server;
    private $clientSocks;
    private $toSent;

    public function __construct($stream)
    {
        $this->server = stream_socket_server($stream, $errno, $errorMessage);
        stream_set_blocking( $this->server, 0 );
        $this->client_socks = array();

        if ($this->server === false) {
            die("Could not bind to socket: $errorMessage");
        }
    }
    
    public function sendChunk($data, $sock) {
        $data = chunk_split($data, 128);
        if(is_array($data)) {
            foreach ($data as $send) {
                fwrite($sock, $send);
            }
        } else {
            fwrite($sock, $data);
        }

    }

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
                    echo 'Connection accepted from ' . 
                         stream_socket_get_name($newClient, true) .
                         '\n';
                             
                    $this->clientSocks[] = $newClient;
                    echo 'Now there are total ' . 
                         count($this->clientSocks) .
                         ' clients.\n';
                }
                         
                //delete the server socket from the read sockets
                unset($readSocks[ array_search($this->server, $readSocks) ]);
            }
                     
            //message from existing client
            foreach($readSocks as $sock) {
                $name = stream_socket_get_name($sock, true);

                $data[] = fread($sock, 128);

                $data = join("", $data);
                $return[] = array('from' => $name,
                                  'data' => $data
                                 );
            }   
        }

        return $return;
    }
}