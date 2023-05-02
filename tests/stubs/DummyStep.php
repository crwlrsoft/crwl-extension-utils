<?php

namespace Tests\stubs;

use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\StepBuilder;
use Generator;

class DummyStep extends StepBuilder
{
    public function stepId(): string
    {
        return 'dummy.step';
    }

    public function label(): string
    {
        return 'This dummy step does nothing';
    }

    public function configToStep(array $stepConfig): StepInterface
    {
        return new class () extends Step
        {
            protected function invoke(mixed $input): Generator
            {
                yield $input;
            }
        };
    }
}