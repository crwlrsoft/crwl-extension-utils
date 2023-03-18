<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\Crawler\Steps\StepInterface;

abstract class StepBuilder
{
    public string $label = 'Get details about a place via google places API';

    abstract public function label(): string;

    abstract public function stepId(): string;

    abstract public function configToStep(array $stepConfig): StepInterface;

    public function group(): string
    {
        return explode('.', $this->stepId())[0];
    }

    /**
     * @return array<string, ConfigParam>
     */
    public function configParams(): array
    {
        return [];
    }
}
