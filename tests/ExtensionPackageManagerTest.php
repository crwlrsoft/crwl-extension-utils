<?php

namespace Tests;

use Crwlr\CrwlExtensionUtils\Exceptions\DuplicateExtensionPackageException;
use Crwlr\CrwlExtensionUtils\ExtensionPackage;
use Crwlr\CrwlExtensionUtils\ExtensionPackageManager;

test('new() creates a new instance on each consecutive call', function () {
    $manager = new ExtensionPackageManager();

    expect($manager->getPackage('firstPackage'))->toBeNull();

    $firstPackage = $manager->registerPackage('firstPackage');

    expect($manager->getPackage('firstPackage'))->toBe($firstPackage);

    $anotherManager = new ExtensionPackageManager();

    expect($anotherManager->getPackage('firstPackage'))->toBeNull();
});

test('registerPackage creates an instance of ExtensionPackage with the package name', function () {
    $registeredPackage = (new ExtensionPackageManager())->registerPackage('fooBar');

    expect($registeredPackage)->toBeInstanceOf(ExtensionPackage::class);

    expect($registeredPackage->name)->toBe('fooBar');
});

it('throws an exception when a package with a certain name is already registered', function () {
    $manager = new ExtensionPackageManager();

    $manager->registerPackage('foo');

    $manager->registerPackage('foo');
})->throws(DuplicateExtensionPackageException::class);

test('getPackage() gets registered packages', function () {
    $manager = new ExtensionPackageManager();

    $packageOne = $manager->registerPackage('foo');

    $packageTwo = $manager->registerPackage('bar');

    expect($manager->getPackage('foo'))->toBe($packageOne);

    expect($manager->getPackage('bar'))->toBe($packageTwo);
});

test('getPackages() returns all registered packages', function () {
    $manager = new ExtensionPackageManager();

    $manager->registerPackage('foo');

    $manager->registerPackage('bar');

    $registeredPackages = $manager->getPackages();

    expect($registeredPackages)->toHaveCount(2);

    expect($registeredPackages['foo']->name)->toBe('foo');

    expect($registeredPackages['bar']->name)->toBe('bar');

    $manager->registerPackage('baz');

    $registeredPackages = $manager->getPackages();

    expect($registeredPackages)->toHaveCount(3);

    expect($registeredPackages['baz']->name)->toBe('baz');
});

test('getStepById() gets a step by its id from one of the registered packages', function () {
    $manager = new ExtensionPackageManager();

    $packageOne = $manager->registerPackage('foo');

    $packageTwo = $manager->registerPackage('bar');

    $stepOne = helper_makeStepBuilder('foo.step');

    expect($manager->getStepById('foo.step'))->toBeNull();

    $packageOne->registerStep($stepOne);

    expect($manager->getStepById('foo.step'))->toBe($stepOne);

    $stepTwo = helper_makeStepBuilder('bar.step');

    expect($manager->getStepById('bar.step'))->toBeNull();

    $packageTwo->registerStep($stepTwo);

    expect($manager->getStepById('bar.step'))
        ->toBe($stepTwo)
        ->and($manager->getStepById('baz.step'))
        ->toBeNull();
});
