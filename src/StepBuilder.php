<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\Crawler\Steps\StepInterface;

abstract class StepBuilder
{
    public readonly string $stepId;

    public readonly string $group;

    public readonly string $label;

    public function __construct()
    {
        $this->stepId = $this->stepId();

        $this->group = $this->group();

        $this->label = $this->label();
    }

    abstract public function stepId(): string;

    abstract public function label(): string;

    abstract public function configToStep(array $stepConfig): StepInterface;

    public function group(): string
    {
        return explode('.', $this->stepId())[0];
    }

    /**
     * @return array<ConfigParam>
     */
    public function configParams(): array
    {
        return [];
    }
}
