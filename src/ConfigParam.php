<?php

namespace Crwlr\CrwlExtensionUtils;

final class ConfigParam
{
    public function __construct(
        public readonly ConfigParamTypes $type,
        public readonly string $name,
        public readonly mixed $value,
    ) {
    }

    public static function bool(string $paramName): self
    {
        return new self(ConfigParamTypes::Bool, $paramName, false);
    }

    public static function int(string $paramName): self
    {
        return new self(ConfigParamTypes::Int, $paramName, 0);
    }

    public static function string(string $paramName): self
    {
        return new self(ConfigParamTypes::String, $paramName, '');
    }

    public function default(mixed $defaultValue): self
    {
        return new self($this->type, $this->name, $defaultValue);
    }

    public function toArray(): array
    {
        return ['type' => $this->type->name, 'name' => $this->name];
    }
}
