<?php

namespace Webleit\ZohoCrmApi\Request;

use BenTools\Psr7\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestMatcher
 * @package Webleit\ZohoCrmApi\Request
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @inheritDoc
     */
    public function matchRequest(RequestInterface $request)
    {
        return true;
    }
}
