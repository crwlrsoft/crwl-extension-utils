<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\Crawler\Steps\StepOutputType;
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

    public readonly string $outputType;

    public readonly bool $isLoadingStep;

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

        $this->outputType = match ($this->outputType()) {
            StepOutputType::Mixed => 'mixed',
            StepOutputType::AssociativeArrayOrObject => 'array',
            StepOutputType::Scalar => 'scalar',
        };

        $this->isLoadingStep = $this->isLoadingStep();
    }

    abstract public function stepId(): string;

    abstract public function label(): string;

    /**
     * @param mixed[] $stepConfig
     */
    abstract public function configToStep(array $stepConfig): StepInterface;

    /**
     * @deprecated In v3.0 it will be required to implement this method in all child classes,
     *             and this default implementation will be changed to an abstract method definition.
     */
    public function outputType(): StepOutputType
    {
        return StepOutputType::Mixed;
    }

    /**
     * When making a StepBuilder for a loading step, please add a child implementation of this method,
     * returning true.
     */
    public function isLoadingStep(): bool
    {
        return false;
    }

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
     * @param mixed[] $configDataArray
     */
    protected function getValueFromConfigArray(string $key, array $configDataArray): mixed
    {
        foreach ($configDataArray as $configDataProperty) {
            if ($configDataProperty['name'] === $key) {
                $configParam = $this->getConfigParam($key);

                if ($configParam) {
                    return $configParam->castValue($configDataProperty['value']);
                }

                return $configDataProperty['value'];
            }
        }

        return null;
    }

    protected function getConfigParam(string $key): ?ConfigParam
    {
        foreach ($this->configParams() as $configParam) {
            if ($configParam->name === $key) {
                return $configParam;
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
