<?php

namespace App\Utils;

class JsonResponse extends Response
{
    public function __construct(mixed $data, int $status = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        parent::__construct(json_encode($data), $status, $headers);
    }
}