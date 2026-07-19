<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\SheetInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\SpreadsheetInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\SheetNotFoundException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Helper\XlsxFileReader;

readonly class XlsxSpreadsheet implements SpreadsheetInterface
{
    public function __construct(
        private string $filepath,
    ) {}

    public static function getExtension(): string
    {
        return 'xlsx';
    }

    public function getSheets(): \Generator
    {
        $reader = XlsxFileReader::createReader($this->filepath, 'workbook');

        try {
            while ($reader->read()) {
                if (\XMLReader::ELEMENT === $reader->nodeType && 'sheet' === $reader->name) {
                    yield new XlsxSheet(
                        filepath: $this->filepath,
                        name: (string) $reader->getAttribute('name'),
                        index: (int) $reader->getAttribute('sheetId'),
                    );
                }
            }
        } finally {
            $reader->close();
        }
    }

    public function getSheetByName(string $name): ?SheetInterface
    {
        foreach ($this->getSheets() as $sheet) {
            if ($sheet->name === $name) {
                return $sheet;
            }
        }

        throw new SheetNotFoundException();
    }

    public function countSheets(): int
    {
        return iterator_count($this->getSheets());
    }
}
