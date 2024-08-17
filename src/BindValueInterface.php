<?php

declare(strict_types=1);

namespace Haikara\SequelPdo;

use Stringable;

interface BindValueInterface
{
    /**
     * @param string|Stringable|int|float|bool|null $value
     * @param int|null $paramType
     */
    public function __construct(
        string|Stringable|int|float|bool|null $value,
        int|null $paramType = null
    );

    /**
     * @return string|Stringable|int|float|bool|null
     */
    public function getValue(): string|Stringable|int|float|bool|null;

    /**
     * @return int
     */
    public function getParamType(): int;
}