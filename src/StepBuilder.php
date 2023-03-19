<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\Crawler\Steps\StepInterface;

abstract class StepBuilder
{
    public readonly string $stepId;

    public readonly string $group;

    public readonly string $label;

    public readonly array $config;

    public function __construct()
    {
        $this->stepId = $this->stepId();

        $this->group = $this->group();

        $this->label = $this->label();

        $this->config = $this->configToArray();
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

    protected function getValueFromConfigArray(string $key, array $configParams): mixed
    {
        foreach ($configParams as $configParam) {
            if ($configParam['name'] === $key) {
                return $configParam['value'];
            }
        }

        return null;
    }

    final protected function configToArray(): array
    {
        $config = [];

        foreach ($this->configParams() as $configParam) {
            $config[] = $configParam->toArray();
        }

        return $config;
    }
}
