<?php

namespace Tests\stubs;

use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Generator;

class InvalidStep
{
    public function stepId(): string
    {
        return 'dummy.invalid.step';
    }

    public function label(): string
    {
        return 'This dummy step is invalid because it does not extend the StepBuilder class';
    }

    /**
     * @param mixed[] $stepConfig
     */
    public function configToStep(array $stepConfig): StepInterface
    {
        return new class extends Step {
            protected function invoke(mixed $input): Generator
            {
                yield $input;
            }
        };
    }
}
