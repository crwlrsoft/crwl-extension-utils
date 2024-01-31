<?php

namespace Tests;

use Crwlr\CrwlExtensionUtils\ConfigParam;
use Crwlr\CrwlExtensionUtils\ConfigParamTypes;

it('makes a bool config param instance', function () {
    $configParam = ConfigParam::bool('foo');

    expect($configParam->type)
        ->toBe(ConfigParamTypes::Bool)
        ->and($configParam->name)
        ->toBe('foo')
        ->and($configParam->value)
        ->toBeFalse();
});

it('makes a string config param instance', function () {
    $configParam = ConfigParam::string('foo');

    expect($configParam->type)
        ->toBe(ConfigParamTypes::String)
        ->and($configParam->name)
        ->toBe('foo')
        ->and($configParam->value)
        ->toBe('');
});

it('makes an int config param instance', function () {
    $configParam = ConfigParam::int('foo');

    expect($configParam->type)
        ->toBe(ConfigParamTypes::Int)
        ->and($configParam->name)
        ->toBe('foo')
        ->and($configParam->value)
        ->toBe(0);
});

it('makes a new instance with a default value', function () {
    $instance = new ConfigParam(
        ConfigParamTypes::Int,
        'foo',
        0,
        'foo input label',
        'foo description',
    );

    $instance2 = $instance->default(5);

    expect($instance2->toArray())
        ->toBe([
            'type' => 'Int',
            'name' => 'foo',
            'value' => 5,
            'inputLabel' => 'foo input label',
            'description' => 'foo description',
        ]);
});

it('makes a new instance with an input label', function () {
    $instance = new ConfigParam(
        ConfigParamTypes::String,
        'foo',
        '',
        '',
        'foo description',
    );

    $instance2 = $instance->inputLabel('label label');

    expect($instance2->toArray())
        ->toBe([
            'type' => 'String',
            'name' => 'foo',
            'value' => '',
            'inputLabel' => 'label label',
            'description' => 'foo description',
        ]);
});

it('makes a new instance with a description', function () {
    $instance = new ConfigParam(
        ConfigParamTypes::Bool,
        'foo',
        true,
        'foo input label',
        '',
    );

    $instance2 = $instance->description('this is a bool config param');

    expect($instance2->toArray())
        ->toBe([
            'type' => 'Bool',
            'name' => 'foo',
            'value' => true,
            'inputLabel' => 'foo input label',
            'description' => 'this is a bool config param',
        ]);
});
