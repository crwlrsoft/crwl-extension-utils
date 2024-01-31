<?php

namespace Tests;

use Crwlr\Crawler\Input;
use Crwlr\Crawler\Output;
use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\ConfigParam;
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

it('correctly gets group when stepId contains a "."', function () {
    $builder = new class () extends StepBuilder {
        public function stepId(): string
        {
            return 'foo.bar';
        }

        public function label(): string
        {
            return 'This is the foo bar step';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class () extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'yo';
                }
            };
        }
    };

    expect($builder->stepId())
        ->toBe('foo.bar')
        ->and($builder->group())
        ->toBe('foo');
});

it('gets a value from a step config array', function () {
    $builder = new class () extends StepBuilder {
        public function stepId(): string
        {
            return 'test.foo';
        }

        public function label(): string
        {
            return 'This is a demo step.';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            $valueFromConfigArray = $this->getValueFromConfigArray('someKey', $stepConfig);

            return new class ($valueFromConfigArray) extends Step {
                public function __construct(private readonly string $value)
                {
                }

                protected function invoke(mixed $input): Generator
                {
                    yield $this->value;
                }
            };
        }
    };

    $stepConfig = [
        [
            'name' => 'someKey',
            'type' => 'String',
            'value' => 'super-duper value',
            'inputLabel' => 'Some Key:',
            'description' => null,
        ],
    ];

    $step = $builder->configToStep($stepConfig);

    $results = iterator_to_array($step->invokeStep(new Input('anything')));

    expect($results)
        ->toHaveCount(1)
        ->and($results[0])
        ->toBeInstanceOf(Output::class)
        ->and($results[0]->get())
        ->toBe('super-duper value');
});

it('turns the config params into an array', function () {
    $builder = new class () extends StepBuilder {
        public function stepId(): string
        {
            return 'test.bar';
        }

        public function label(): string
        {
            return 'This is another demo step.';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class () extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'foo';
                }
            };
        }

        /**
         * @return array<int, ConfigParam>
         */
        public function configParams(): array
        {
            return [
                ConfigParam::string('foo')
                    ->inputLabel('foo input label')
                    ->description('foo description'),

                ConfigParam::int('number')
                    ->inputLabel('number input label')
                    ->description('number description'),

                ConfigParam::bool('someBool')
                    ->inputLabel('Input label for some bool')
                    ->description('This is a boolean config param')
            ];
        }
    };

    expect($builder->config)->toBe([
        [
            'type' => 'String',
            'name' => 'foo',
            'value' => '',
            'inputLabel' => 'foo input label',
            'description' => 'foo description',
        ],
        [
            'type' => 'Int',
            'name' => 'number',
            'value' => 0,
            'inputLabel' => 'number input label',
            'description' => 'number description',
        ],
        [
            'type' => 'Bool',
            'name' => 'someBool',
            'value' => false,
            'inputLabel' => 'Input label for some bool',
            'description' => 'This is a boolean config param',
        ],
    ]);
});
