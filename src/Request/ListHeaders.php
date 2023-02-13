<?php


namespace Webleit\ZohoCrmApi\Request;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ListHeaders implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @var array<string,mixed>
     */
    protected array $headers = [
        'If-Modified-Since' => null,
        'Authorization' => null,
    ];

    /**
     * @param array<string,mixed> $headers
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
    }

    /**
     * @param array<string,mixed> $headers
     */
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

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return array_filter($this->headers);
    }

    public function toJson($options = 0): string|false
    {
        return json_encode($options);
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string,mixed>
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }
}
