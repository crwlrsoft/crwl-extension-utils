<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\CrwlExtensionUtils\Exceptions\InvalidStepBuilderException;

abstract class StepBuilder
{
    public readonly string $stepId;

    public readonly string $group;

    public readonly string $label;

    /**
     * @var array<int, array<string, mixed>>
     */
    public readonly array $config;

    protected ?string $fileStoragePath = null;

    /**
     * @throws InvalidStepBuilderException
     */
    public function __construct()
    {
        $this->stepId = $this->stepId();

        $this->group = $this->group();

        $this->label = $this->label();

        $this->config = $this->configToArray();
    }

    abstract public function stepId(): string;

    abstract public function label(): string;

    /**
     * @param mixed[] $stepConfig
     */
    abstract public function configToStep(array $stepConfig): StepInterface;

    /**
     * @throws InvalidStepBuilderException
     */
    public function group(): string
    {
        if (!str_contains($this->stepId(), '.')) {
            throw new InvalidStepBuilderException('The stepId must contain a "." to separate group and step name.');
        }

        return explode('.', $this->stepId())[0];
    }

    /**
     * @return array<ConfigParam>
     */
    public function configParams(): array
    {
        return [];
    }

    public function setFileStoragePath(string $path): void
    {
        $this->fileStoragePath = $path;
    }

    /**
     * @param mixed[] $configParams
     */
    protected function getValueFromConfigArray(string $key, array $configParams): mixed
    {
        foreach ($configParams as $configParam) {
            if ($configParam['name'] === $key) {
                return $configParam['value'];
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    final protected function configToArray(): array
    {
        $config = [];

        foreach ($this->configParams() as $configParam) {
            $config[] = $configParam->toArray();
        }

        return $config;
    }
}
