<?php

namespace Native\Laravel\Clients;

use Native\Laravel\Contracts\Client;

class Tauri implements Client
{
    public function get(string $resource)
    {
        return $this->request([
            'invoke' => $resource,
        ]);
    }

    public function post(string $resource, array $data = [])
    {
        return $this->request([
            'invoke' => $resource,
            'data' => $data,
        ]);
    }

    public function delete(string $resource, array $data = [])
    {
        return $this->request([
            'invoke' => $resource,
            'data' => $data,
        ]);
    }

    protected function request($message)
    {
        // Connect to the socket, send the data and get a response and shutdown
        $client = stream_socket_client('tcp://127.0.0.1:9000');

        stream_socket_sendto($client, json_encode($message));

        $response = base_convert(stream_socket_recvfrom($client, 1500000), 2, 10);

        stream_socket_shutdown($client, STREAM_SHUT_RDWR);

        return $response;
    }
}
