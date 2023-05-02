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

test('getStepById() gets a step by its id from one of the registered packages', function () {
    $manager = ExtensionPackageManager::new();

    $packageOne = $manager->registerPackage('foo');

    $packageTwo = $manager->registerPackage('bar');

    $stepOne = helper_makeStepBuilder('fooStep');

    expect($manager->getStepById('fooStep'))->toBeNull();

    $packageOne->registerStep($stepOne);

    expect($manager->getStepById('fooStep'))->toBe($stepOne);

    $stepTwo = helper_makeStepBuilder('barStep');

    expect($manager->getStepById('barStep'))->toBeNull();

    $packageTwo->registerStep($stepTwo);

    expect($manager->getStepById('barStep'))->toBe($stepTwo);

    expect($manager->getStepById('bazStep'))->toBeNull();
});
