<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Contract;

interface CoordinateInterface
{
    public string $column { get; }
    public int $row { get; }

    public function __toString(): string;

    public static function fromString(string $coordinate): CoordinateInterface;
}
