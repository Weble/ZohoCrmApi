<?php


namespace Webleit\ZohoCrmApi\Request;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ListHeaders implements Arrayable, Jsonable, JsonSerializable
{
    protected $headers = [
        'If-Modified-Since' => null,
        'Authorization' => null,
    ];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
    }

    public function with(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    public function withAccessToken(string $token): self
    {
        $this->headers['Authorization'] = 'Zoho-oauthtoken '. $token;

        return $this;
    }

    public function modifiedSince(DateTimeInterface $dateTime): self
    {
        $this->headers['If-Modified-Since'] = $dateTime->format(DATE_ATOM);

        return $this;
    }

    public function toArray(): array
    {
        return array_filter($this->headers);
    }

    public function toJson($options = 0): string
    {
        return json_encode($options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }
}
