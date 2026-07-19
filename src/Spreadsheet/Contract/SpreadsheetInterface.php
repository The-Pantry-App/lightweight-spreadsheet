<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Contract;

interface SpreadsheetInterface
{
    public static function getExtension(): string;

    /**
     * @return \Generator<int, SheetInterface>
     */
    public function getSheets(): \Generator;

    public function getSheetByName(string $name): ?SheetInterface;

    public function countSheets(): int;
}
