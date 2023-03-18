<?php

namespace Crwlr\CrwlExtensionUtils;

final class ConfigParam
{
    public function __construct(
        public readonly ConfigParamTypes $type,
        public readonly string $name,
    ) {
    }

    public static function bool(string $paramName): self
    {
        return new self(ConfigParamTypes::Bool, $paramName);
    }

    public static function int(string $paramName): self
    {
        return new self(ConfigParamTypes::Int, $paramName);
    }

    public static function string(string $paramName): self
    {
        return new self(ConfigParamTypes::String, $paramName);
    }

    public function toArray(): array
    {
        return ['type' => $this->type->name, 'name' => $this->name];
    }
}
