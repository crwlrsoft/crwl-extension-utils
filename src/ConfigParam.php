<?php

namespace Crwlr\CrwlExtensionUtils;

final class ConfigParam
{
    public function __construct(
        public readonly ConfigParamTypes $type,
        public readonly string $name,
        public readonly mixed $value,
        public readonly string $inputLabel = '',
        public readonly string $description = '',
    ) {}

    public static function bool(string $paramName): self
    {
        return new self(ConfigParamTypes::Bool, $paramName, false);
    }

    public static function int(string $paramName): self
    {
        return new self(ConfigParamTypes::Int, $paramName, 0);
    }

    public static function float(string $paramName): self
    {
        return new self(ConfigParamTypes::Float, $paramName, 0.0);
    }

    public static function string(string $paramName): self
    {
        return new self(ConfigParamTypes::String, $paramName, '');
    }

    public static function multiLineString(string $paramName): self
    {
        return new self(ConfigParamTypes::MultiLineString, $paramName, '');
    }

    public function default(mixed $defaultValue): self
    {
        return new self($this->type, $this->name, $defaultValue, $this->inputLabel, $this->description);
    }

    public function inputLabel(string $label): self
    {
        return new self($this->type, $this->name, $this->value, $label, $this->description);
    }

    public function description(string $description): self
    {
        return new self($this->type, $this->name, $this->value, $this->inputLabel, $description);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->name,
            'name' => $this->name,
            'value' => $this->value,
            'inputLabel' => $this->inputLabel,
            'description' => $this->description,
        ];
    }

    public function castValue(mixed $value): mixed
    {
        if ($this->type === ConfigParamTypes::Bool) {
            return is_bool($value) ? $value : (bool) $value;
        } elseif ($this->type === ConfigParamTypes::Int) {
            return is_int($value) ? $value : (int) $value;
        } elseif ($this->type === ConfigParamTypes::Float) {
            return is_float($value) ? $value : (float) $value;
        } elseif ($this->type === ConfigParamTypes::String || $this->type === ConfigParamTypes::MultiLineString) {
            return is_string($value) ? $value : (string) $value;
        }

        return $value; // @phpstan-ignore-line
    }
}
