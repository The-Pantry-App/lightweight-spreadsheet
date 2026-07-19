<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\ValueObject;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CoordinateInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\InvalidCoordinateException;

class Coordinate implements CoordinateInterface
{
    public function __construct(
        public private(set) string $column,
        public private(set) int $row,
    ) {
        $this->column = strtoupper($this->column);
    }

    public function __toString(): string
    {
        return $this->column . $this->row;
    }

    public static function fromString(string $coordinate): CoordinateInterface
    {
        if (!preg_match('/^([A-Za-z]+)(\d+)$/', $coordinate, $matches)) {
            throw new InvalidCoordinateException();
        }

        return new self($matches[1], (int) $matches[2]);
    }
}
