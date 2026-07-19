<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Contract;

interface SheetInterface
{
    public string $name { get; }

    public int $index { get; }

    /**
     * @return \Generator<int, RowInterface>
     */
    public function getRows(): \Generator;

    public function getRow(int $index): ?RowInterface;

    public function countRows(): int;

    public function getCell(CoordinateInterface|string $coordinate): ?CellInterface;
}
