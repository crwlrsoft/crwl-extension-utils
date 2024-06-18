<?php

namespace Crwlr\CrwlExtensionUtils;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RequestTracker
{
    /**
     * @var Closure[]
     */
    private array $onHttpResponse = [];

    /**
     * @var Closure[]
     */
    private array $onHeadlessBrowserResponse = [];

    public function onHttpResponse(Closure $closure): self
    {
        $this->onHttpResponse[] = $closure;

        return $this;
    }

    public function onHeadlessBrowserResponse(Closure $closure): self
    {
        $this->onHeadlessBrowserResponse[] = $closure;

        return $this;
    }

    public function trackHttpResponse(?RequestInterface $request = null, ?ResponseInterface $response = null): void
    {
        foreach ($this->onHttpResponse as $closure) {
            $closure->call($this, $request, $response);
        }
    }

    public function trackHeadlessBrowserResponse(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
    ): void {
        foreach ($this->onHeadlessBrowserResponse as $closure) {
            $closure->call($this, $request, $response);
        }
    }
}
