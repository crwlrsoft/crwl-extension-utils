<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use Crwlr\Crawler\Steps\Step;
use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\StepBuilder;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function helper_makeStepBuilder(string $stepId): StepBuilder
{
    return new class ($stepId) extends StepBuilder
    {
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
            return new class () extends Step
            {
                protected function invoke(mixed $input): Generator
                {
                    yield $input;
                }
            };
        }
    };
}