<?php

namespace Crwlr\CrwlExtensionUtils;

use Crwlr\CrwlExtensionUtils\Exceptions\DuplicateExtensionPackageException;

final class ExtensionPackageManager
{
    /**
     * @var ExtensionPackage[]
     */
    private array $packages = [];

    private static ?self $instance = null;

    public static function singleton(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @throws DuplicateExtensionPackageException
     */
    public function registerPackage(string $name): ExtensionPackage
    {
        if ($this->getPackage($name) !== null) {
            throw new DuplicateExtensionPackageException('An extension with that name is already registered.');
        }

        $this->packages[$name] = new ExtensionPackage($this, $name);

        return $this->packages[$name];
    }

    /**
     * @return ExtensionPackage[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getPackage(string $name): ?ExtensionPackage
    {
        if (array_key_exists($name, $this->packages)) {
            return $this->packages[$name];
        }

        return null;
    }

    public function getStepById(string $stepId): ?StepBuilder
    {
        foreach ($this->packages as $package) {
            $step = $package->getStep($stepId);

            if ($step) {
                return $step;
            }
        }

        return null;
    }
}
