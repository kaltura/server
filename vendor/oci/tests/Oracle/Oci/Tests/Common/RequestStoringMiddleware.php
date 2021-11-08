<?php

namespace Oracle\Oci\Tests\Common;

use Psr\Http\Message\RequestInterface;

class RequestStoringMiddleware
{
    protected $requests = [];

    public function storeRequests()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $this->requests[] = $request;
                return $handler($request, $options);
            };
        };
    }

    public function getRequests()
    {
        return $this->requests;
    }
}
