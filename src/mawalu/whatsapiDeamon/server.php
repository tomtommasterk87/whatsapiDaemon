<?php

namespace mawalu\whatsapiDeamon;

class server
{
    private $server;
    private $client_socks;

    public function __construct($stream)
    {
        $this->server = stream_socket_server($stream, $errno, $errorMessage);
        stream_set_blocking( $this->server, 0 );
        $this->client_socks = array();

        if ($this->server === false) {
            die("Could not bind to socket: $errorMessage");
        }
    }

    public function socket()
    {
        $read_socks = $this->client_socks;
        $read_socks[] = $this->server;
        $return = array();
                 
        //start reading and use a large timeout
        if(stream_select ( $read_socks, $write, $except, 1 )) {
            //new client
            if(in_array($this->server, $read_socks)) {
                $new_client = stream_socket_accept($this->server);
                         
                if ($new_client) {
                    //print remote client information, ip and port number
                    echo 'Connection accepted from ' . stream_socket_get_name($new_client, true) . "\n";
                             
                    $this->client_socks[] = $new_client;
                    echo "Now there are total ". count($this->client_socks) . " clients.\n";
                }
                         
                //delete the server socket from the read sockets
                unset($read_socks[ array_search($this->server, $read_socks) ]);
            }
                     
            //message from existing client
            foreach($read_socks as $sock) {
                print_r($sock);
                $data[] = fread($sock, 128);

                $data = join($data);
                $return[] = array("from" => stream_socket_get_name($sock, true),
                                  "data" => $data
                                 );
            }   
        }

        return $return;
    }
}