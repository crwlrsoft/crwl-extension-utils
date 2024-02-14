<?php

namespace Crwlr\CrwlExtensionUtils;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

final class TrackingGuzzleClientFactory
{
    public function __construct(private readonly RequestTracker $requestTracker) {}

    /**
     * @param mixed[] $withOptions
     */
    public function getClient(array $withOptions = []): Client
    {
        $stack = array_key_exists('handler', $withOptions) ? $withOptions['handler'] : HandlerStack::create();

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->requestTracker->trackHttpResponse(response: $response);

            return $response;
        }));

        $withOptions['handler'] = $stack;

        return new Client($withOptions);
    }
}
