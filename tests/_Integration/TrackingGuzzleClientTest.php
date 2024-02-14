<?php

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

it('tracks an HTTP response', function () {
    $client = helper_getTrackingGuzzleClient();

    $trackedResponses = [];

    $tracker = helper_getRequestTracker();

    $tracker->onHttpResponse(
        function (?RequestInterface $request, ?ResponseInterface $response) use (&$trackedResponses) {
            $trackedResponses[] = $response;
        }
    );

    expect($trackedResponses)->toHaveCount(0);

    $request = new Request('GET', 'http://localhost:8000/');

    $response = $client->sendRequest($request);

    expect($trackedResponses)
        ->toHaveCount(1)
        ->and($trackedResponses[0])
        ->toBe($response)
        ->and($trackedResponses[0]?->getBody()?->getContents())
        ->toContain('<h1>Hello World!</h1>');
});

it('tracks an HTTP response also when it is a client error response (4xx)', function () {
    $client = helper_getTrackingGuzzleClient();

    $trackedResponses = [];

    $tracker = helper_getRequestTracker();

    $tracker->onHttpResponse(
        function (?RequestInterface $request, ?ResponseInterface $response) use (&$trackedResponses) {
            $trackedResponses[] = $response;
        }
    );

    expect($trackedResponses)->toHaveCount(0);

    $request = new Request('GET', 'http://localhost:8000/client-error-response');

    $response = $client->sendRequest($request);

    expect($trackedResponses)
        ->toHaveCount(1)
        ->and($trackedResponses[0])
        ->toBe($response)
        ->and($trackedResponses[0]?->getStatusCode())
        ->toBeGreaterThanOrEqual(400);
});

it('tracks an HTTP response also when it is a server error response (5xx)', function () {
    $client = helper_getTrackingGuzzleClient();

    $trackedResponses = [];

    $tracker = helper_getRequestTracker();

    $tracker->onHttpResponse(
        function (?RequestInterface $request, ?ResponseInterface $response) use (&$trackedResponses) {
            $trackedResponses[] = $response;
        }
    );

    expect($trackedResponses)->toHaveCount(0);

    $request = new Request('GET', 'http://localhost:8000/server-error-response');

    $response = $client->sendRequest($request);

    expect($trackedResponses)
        ->toHaveCount(1)
        ->and($trackedResponses[0])
        ->toBe($response)
        ->and($trackedResponses[0]?->getStatusCode())
        ->toBeGreaterThanOrEqual(500);
});
