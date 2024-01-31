<?php

namespace Tests;

use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\Exceptions\InvalidStepBuilderException;
use Crwlr\CrwlExtensionUtils\StepBuilder;
use Generator;

it('throws an exception when the stepId() does not contain a "."', function () {
    new class () extends StepBuilder {
        public function stepId(): string
        {
            return 'groupName-stepName';
        }

        public function label(): string
        {
            return 'Step Label';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class () extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'yolo';
                }
            };
        }
    };
})->throws(InvalidStepBuilderException::class);
