<?php

declare(strict_types=1);

namespace Haikara\SequelPdo;

use PDO;
use Stringable;

class BindValue implements BindValueInterface
{
    protected string|Stringable|int|float|bool|null $value;

    protected int $paramType;

    /**
     * @inheritDoc
     */
    public function __construct(
        string|Stringable|int|float|bool|null $value,
        int|null $paramType = null
    ) {
        $this->value = $value;
        $this->paramType = $paramType ?? static::dynamicParamType($value);
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string|Stringable|int|float|bool|null
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getParamType(): int {
        return $this->paramType;
    }

    /**
     * 値の型に対応するPDOのPARAM_*定数を取得する
     *
     * @param string|Stringable|int|float|bool|null $value
     * @return int
     */
    protected static function dynamicParamType(string|Stringable|int|float|bool|null $value): int {

        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR
        };
    }
}