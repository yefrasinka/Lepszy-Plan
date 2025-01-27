<?php

namespace App\Utils;

class RequestContext
{
    private string $method;

    public function fromRequest(Request $request): void
    {
        $this->method = $request->getMethod();
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}