<?php

namespace Tests;

use Crwlr\CrwlExtensionUtils\RequestTracker;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

it('calls all the callbacks registered via onHttpResponse()', function () {
    $callbacksCalled = [];

    $tracker = new RequestTracker();

    $tracker->onHttpResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'one';
    });

    $tracker->onHttpResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'two';
    });

    $tracker->onHttpResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'three';
    });

    expect($callbacksCalled)->toHaveCount(0);

    $tracker->trackHttpResponse();

    expect($callbacksCalled)->toBe(['one', 'two', 'three']);
});

it('calls the callback with the provided request and response objects for http responses', function () {
    $request = new Request('GET', 'https://www.crwlr.software/packages');

    $response = new Response(200, body: 'Hello!');

    $tracker = new RequestTracker();

    $callbackCalled = false;

    $tracker->onHttpResponse(
        function (?RequestInterface $request, ?ResponseInterface $response) use (& $callbackCalled) {
            expect($request)
                ->toBeInstanceOf(RequestInterface::class)
                ->and($response)
                ->toBeInstanceOf(ResponseInterface::class);

            $callbackCalled = true;
        },
    );

    $tracker->trackHttpResponse($request, $response);

    expect($callbackCalled)->toBeTrue();
});

it('calls all the callbacks registered via onHeadlessBrowserResponse()', function () {
    $callbacksCalled = [];

    $tracker = new RequestTracker();

    $tracker->onHeadlessBrowserResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'one';
    });

    $tracker->onHeadlessBrowserResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'two';
    });

    $tracker->onHeadlessBrowserResponse(function () use (& $callbacksCalled) {
        $callbacksCalled[] = 'three';
    });

    expect($callbacksCalled)->toHaveCount(0);

    $tracker->trackHeadlessBrowserResponse();

    expect($callbacksCalled)->toBe(['one', 'two', 'three']);
});

it('calls the callback with the provided request and response objects for headless browser responses', function () {
    $request = new Request('GET', 'https://www.crwlr.software/packages');

    $response = new Response(200, body: 'Hello!');

    $tracker = new RequestTracker();

    $callbackCalled = false;

    $tracker->onHeadlessBrowserResponse(
        function (?RequestInterface $request, ?ResponseInterface $response) use (& $callbackCalled) {
            expect($request)
                ->toBeInstanceOf(RequestInterface::class)
                ->and($response)
                ->toBeInstanceOf(ResponseInterface::class);

            $callbackCalled = true;
        },
    );

    $tracker->trackHeadlessBrowserResponse($request, $response);

    expect($callbackCalled)->toBeTrue();
});
