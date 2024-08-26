<?php

namespace Tests;

use Crwlr\Crawler\Input;
use Crwlr\Crawler\Output;
use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\Crawler\Steps\StepOutputType;
use Crwlr\CrwlExtensionUtils\ConfigParam;
use Crwlr\CrwlExtensionUtils\Exceptions\InvalidStepBuilderException;
use Crwlr\CrwlExtensionUtils\StepBuilder;
use Generator;

it('throws an exception when the stepId() does not contain a "."', function () {
    new class extends StepBuilder {
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
            return new class extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'yolo';
                }
            };
        }
    };
})->throws(InvalidStepBuilderException::class);

it('correctly gets group when stepId contains a "."', function () {
    $builder = new class extends StepBuilder {
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
            return new class extends Step {
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

it(
    'returns StepOutputType::Mixed from the outputType() method, when nothing else is defined in the child class',
    function () {
        $builder = new class extends StepBuilder {
            public function stepId(): string
            {
                return 'foo.baz';
            }

            public function label(): string
            {
                return 'This is the foo baz step';
            }

            public function configToStep(array $stepConfig): StepInterface
            {
                return new class extends Step {
                    protected function invoke(mixed $input): Generator
                    {
                        yield 'hello';
                    }
                };
            }
        };

        expect($builder->outputType())->toBe(StepOutputType::Mixed)
            ->and($builder->outputType)->toBe('mixed');
    },
);

test('you can implement an outputType() method in a child class', function () {
    $builder = new class extends StepBuilder {
        public function stepId(): string
        {
            return 'baz.quz';
        }

        public function label(): string
        {
            return 'This is the baz quz step';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'hey';
                }
            };
        }

        public function outputType(): StepOutputType
        {
            return StepOutputType::Scalar;
        }
    };

    expect($builder->outputType())->toBe(StepOutputType::Scalar)
        ->and($builder->outputType)->toBe('scalar');
});

it('writes values from (abstract) methods, to class properties in the constructor', function () {
    $builder = new class extends StepBuilder {
        public function stepId(): string
        {
            return 'ser.vus';
        }

        public function label(): string
        {
            return 'Hey Servus';
        }

        public function configToStep(array $stepConfig): StepInterface
        {
            return new class extends Step {
                protected function invoke(mixed $input): Generator
                {
                    yield 'servus';
                }
            };
        }

        public function outputType(): StepOutputType
        {
            return StepOutputType::AssociativeArrayOrObject;
        }
    };

    expect($builder->stepId)->toBe('ser.vus')
        ->and($builder->group)->toBe('ser')
        ->and($builder->label)->toBe('Hey Servus')
        ->and($builder->config)->toBe([])
        ->and($builder->outputType)->toBe('array');
});

it('gets a value from a step config array', function () {
    $builder = new class extends StepBuilder {
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
                public function __construct(private readonly string $value) {}

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
    $builder = new class extends StepBuilder {
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
            return new class extends Step {
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
                    ->description('This is a boolean config param'),
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

it('casts config values based on their configured type when using getValueFromConfigArray()', function () {
    $builder = new class extends StepBuilder {
        public function stepId(): string
        {
            return 'foo.bar';
        }

        public function label(): string
        {
            return 'Demo step.';
        }

        /**
         * @param mixed[] $stepConfig
         * @return StepInterface
         */
        public function configToStep(array $stepConfig): StepInterface
        {
            $values = [
                'bool' => $this->getValueFromConfigArray('bool', $stepConfig),
                'int' => $this->getValueFromConfigArray('int', $stepConfig),
                'float' => $this->getValueFromConfigArray('float', $stepConfig),
                'string' => $this->getValueFromConfigArray('string', $stepConfig),
                'multiLineString' => $this->getValueFromConfigArray('multiLineString', $stepConfig),
                'notExisting' => $this->getValueFromConfigArray('notExisting', $stepConfig),
            ];

            return new class ($values) extends Step {
                /**
                 * @param array<string, mixed> $values
                 */
                public function __construct(public readonly array $values) {}

                protected function invoke(mixed $input): Generator
                {
                    yield 'servas';
                }
            };
        }

        /**
         * @return array<int, ConfigParam>
         */
        public function configParams(): array
        {
            return [
                ConfigParam::bool('bool'),
                ConfigParam::int('int'),
                ConfigParam::float('float'),
                ConfigParam::string('string'),
                ConfigParam::multiLineString('multiLineString'),
            ];
        }
    };

    $stepConfig = [
        [
            'name' => 'bool',
            'type' => 'Bool',
            'value' => '1',
            'inputLabel' => '',
            'description' => '',
        ],
        [
            'name' => 'int',
            'type' => 'Int',
            'value' => '1',
            'inputLabel' => '',
            'description' => '',
        ],
        [
            'name' => 'float',
            'type' => 'Float',
            'value' => '1.32',
            'inputLabel' => '',
            'description' => '',
        ],
        [
            'name' => 'string',
            'type' => 'String',
            'value' => 123,
            'inputLabel' => '',
            'description' => '',
        ],
        [
            'name' => 'multiLineString',
            'type' => 'MultiLineString',
            'value' => 12345,
            'inputLabel' => '',
            'description' => '',
        ],
    ];

    $step = $builder->configToStep($stepConfig);

    expect($step->values)->toBe([ // @phpstan-ignore-line
        'bool' => true,
        'int' => 1,
        'float' => 1.32,
        'string' => '123',
        'multiLineString' => '12345',
        'notExisting' => null,
    ]);
});
