<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Contract;

interface RowInterface
{
    public int $rowIndex { get; }

    /**
     * @return array<string, CellInterface>
     */
    public function getCells(): array;

    public function getCell(string $columnLetter): ?CellInterface;

    public function isEmpty(): bool;
}
