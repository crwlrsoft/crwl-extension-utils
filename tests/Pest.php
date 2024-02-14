<?php

use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\RequestTracker;
use Crwlr\CrwlExtensionUtils\StepBuilder;
use Crwlr\CrwlExtensionUtils\TrackingGuzzleClientFactory;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

class TestServerProcess
{
    public static ?Process $process = null;
}

uses()
    ->group('integration')
    ->beforeEach(function () {
        if (!isset(TestServerProcess::$process)) {
            TestServerProcess::$process = Process::fromShellCommandline(
                'php -S localhost:8000 ' . __DIR__ . '/_Integration/Server.php'
            );

            TestServerProcess::$process->start();

            usleep(100000);
        }
    })
    ->afterAll(function () {
        TestServerProcess::$process?->stop(3, SIGINT);

        TestServerProcess::$process = null;
    })
    ->in('_Integration');

function helper_makeStepBuilder(string $stepId): StepBuilder
{
    return new class ($stepId) extends StepBuilder {
        public function __construct(private readonly string $stepIdArg)
        {
            parent::__construct();
        }

        public function stepId(): string
        {
            return $this->stepIdArg;
        }

        public function label(): string
        {
            return $this->stepIdArg . ' label';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class () extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield $input;
                }
            };
        }
    };
}

/**
 * @throws BindingResolutionException
 */
function helper_getTrackingGuzzleClient(): Client
{
    return app()->make(TrackingGuzzleClientFactory::class)->getClient();
}

/**
 * @throws BindingResolutionException
 */
function helper_getRequestTracker(): RequestTracker
{
    return app()->make(RequestTracker::class);
}
