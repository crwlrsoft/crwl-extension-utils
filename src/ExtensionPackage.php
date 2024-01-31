<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\CrwlExtensionUtils\Exceptions\DuplicateStepIdException;
use Crwlr\CrwlExtensionUtils\Exceptions\InvalidStepException;

final class ExtensionPackage
{
    /**
     * @var array<string, StepBuilder>
     */
    private array $steps = [];

    public function __construct(
        private readonly ExtensionPackageManager $manager,
        public readonly string $name = '',
    ) {}

    /**
     * @param array<string|StepBuilder> $steps
     * @throws DuplicateStepIdException
     * @throws InvalidStepException
     */
    public function registerSteps(array $steps): self
    {
        foreach ($steps as $step) {
            $this->registerStep($step);
        }

        return $this;
    }

    /**
     * @throws DuplicateStepIdException
     * @throws InvalidStepException
     */
    public function registerStep(string|StepBuilder $step): self
    {
        if (is_string($step)) {
            if (!class_exists($step)) {
                throw new InvalidStepException('Class does not exist.');
            }

            $step = new $step();

            if (!$step instanceof StepBuilder) {
                throw new InvalidStepException('Provided step class is not an instance of ' . StepBuilder::class);
            }
        }

        if ($this->manager->getStepById($step->stepId()) !== null) {
            throw new DuplicateStepIdException('A step with this ID is already registered.');
        }

        $this->steps[$step->stepId()] = $step;

        return $this;
    }

    public function getStep(string $stepId): ?StepBuilder
    {
        if (array_key_exists($stepId, $this->steps)) {
            return $this->steps[$stepId];
        }

        return null;
    }

    /**
     * @return array<string, StepBuilder>
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
}
