<?php

namespace Tests;

use Crwlr\CrwlExtensionUtils\Exceptions\DuplicateExtensionPackageException;
use Crwlr\CrwlExtensionUtils\ExtensionPackage;
use Crwlr\CrwlExtensionUtils\ExtensionPackageManager;

test('singleton() creates an instance at the first call and always returns that one afterwards', function () {
    $manager = ExtensionPackageManager::singleton();

    expect($manager->getPackage('firstPackage'))->toBeNull();

    $firstPackage = $manager->registerPackage('firstPackage');

    expect($manager->getPackage('firstPackage'))->toBe($firstPackage);

    $anotherManager = ExtensionPackageManager::singleton();

    expect($anotherManager->getPackage('firstPackage'))->toBe($firstPackage);
});

test('new() creates a new instance on each consecutive call', function () {
    $manager = ExtensionPackageManager::new();

    expect($manager->getPackage('firstPackage'))->toBeNull();

    $firstPackage = $manager->registerPackage('firstPackage');

    expect($manager->getPackage('firstPackage'))->toBe($firstPackage);

    $anotherManager = ExtensionPackageManager::new();

    expect($anotherManager->getPackage('firstPackage'))->toBeNull();
});

test('registerPackage creates an instance of ExtensionPackage with the package name', function () {
    $registeredPackage = ExtensionPackageManager::new()->registerPackage('fooBar');

    expect($registeredPackage)->toBeInstanceOf(ExtensionPackage::class);

    expect($registeredPackage->name)->toBe('fooBar');
});

it('throws an exception when a package with a certain name is already registered', function () {
    ExtensionPackageManager::singleton()->registerPackage('foo');

    ExtensionPackageManager::singleton()->registerPackage('foo');
})->throws(DuplicateExtensionPackageException::class);

test('getPackage() gets registered packages', function () {
    $manager = ExtensionPackageManager::new();

    $packageOne = $manager->registerPackage('foo');

    $packageTwo = $manager->registerPackage('bar');

    expect($manager->getPackage('foo'))->toBe($packageOne);

    expect($manager->getPackage('bar'))->toBe($packageTwo);
});

test('getPackages() returns all registered packages', function () {
    $manager = ExtensionPackageManager::new();

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
    $manager = ExtensionPackageManager::new();

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
