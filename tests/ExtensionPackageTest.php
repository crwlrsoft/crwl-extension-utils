<?php

namespace Tests;

use Crwlr\CrwlExtensionUtils\Exceptions\DuplicateStepIdException;
use Crwlr\CrwlExtensionUtils\Exceptions\InvalidStepException;
use Crwlr\CrwlExtensionUtils\ExtensionPackage;
use Crwlr\CrwlExtensionUtils\ExtensionPackageManager;
use Tests\stubs\DummyStep;
use Tests\stubs\InvalidStep;

it('registers a step with an instance of the StepBuilder class', function () {
    $package = new ExtensionPackage(ExtensionPackageManager::new(), 'test-package');

    $stepBuilder = helper_makeStepBuilder('foo.step');

    $package->registerStep($stepBuilder);

    expect($package->getStep('foo.step'))->toBe($stepBuilder);
});

it('registers a step with the full class name string', function () {
    $package = new ExtensionPackage(ExtensionPackageManager::new(), 'test-package');

    $package->registerStep(DummyStep::class);

    expect($package->getStep('dummy.step'))->toBeInstanceOf(DummyStep::class);
});

it('throws an exception if the provided class name does not exist', function () {
    $package = new ExtensionPackage(ExtensionPackageManager::new(), 'test-package');

    $package->registerStep('UnknownStepBuilder');
})->throws(InvalidStepException::class);

it('throws an exception if the provided class name is not does not extend the StepBuilder class', function () {
    $package = new ExtensionPackage(ExtensionPackageManager::new(), 'test-package');

    $package->registerStep(InvalidStep::class);
})->throws(InvalidStepException::class);

it('throws an exception if another step with the same ID is registered already', function () {
    $manager = ExtensionPackageManager::new();

    $package = $manager->registerPackage('test-package');

    $package->registerStep(DummyStep::class);

    expect($package->getStep('dummy.step'))->toBeInstanceOf(DummyStep::class);

    $package->registerStep(DummyStep::class);
})->throws(DuplicateStepIdException::class);

it(
    'also throws an exception if another step with the same ID is registered already in another package, registered ' .
    'in the same manager',
    function () {
        $manager = ExtensionPackageManager::new();

        $package = $manager->registerPackage('test-package');

        $package->registerStep(DummyStep::class);

        expect($package->getStep('dummy.step'))->toBeInstanceOf(DummyStep::class);

        $anotherPackage = $manager->registerPackage('another-package');

        $anotherPackage->registerStep(DummyStep::class);
    }
)->throws(DuplicateStepIdException::class);

test('getSteps() returns all steps registered for this package', function () {
    $manager = ExtensionPackageManager::new();

    $package = $manager->registerPackage('test-package');

    $anotherPackage = $manager->registerPackage('another-package');

    $package->registerStep(helper_makeStepBuilder('test-package.foo'));

    $package->registerStep(helper_makeStepBuilder('test-package.bar'));

    $package->registerStep(helper_makeStepBuilder('test-package.baz'));

    $anotherPackage->registerStep(helper_makeStepBuilder('another-package.quz'));

    expect($package->getSteps())
        ->toHaveCount(3)
        ->and($anotherPackage->getSteps())
        ->toHaveCount(1);

});

it('registered multiple steps in one call with registerSteps()', function () {
    $manager = ExtensionPackageManager::new();

    $package = $manager->registerPackage('test-package');

    $package->registerSteps([
        helper_makeStepBuilder('test-package.foo'),
        helper_makeStepBuilder('test-package.bar'),
        helper_makeStepBuilder('test-package.baz'),
    ]);

    expect($package->getSteps())->toHaveCount(3);
});
