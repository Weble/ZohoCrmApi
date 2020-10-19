<?php

namespace Webleit\ZohoCrmApi\Request;

use BenTools\Psr7\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;

class RequestMatcher implements RequestMatcherInterface
{
    public function matchRequest(RequestInterface $request)
    {
        return true;
    }
}
