<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CellInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CoordinateInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\RowInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\SheetInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Helper\XlsxFileReader;
use ThePantry\LightweightSpreadsheet\Spreadsheet\ValueObject\Coordinate;

class XlsxSheet implements SheetInterface
{
    public function __construct(
        private readonly string $filepath,
        public string $name,
        public int $index,
    ) {}

    /**
     * @return \Generator<int, RowInterface>
     */
    public function getRows(): \Generator
    {
        $reader = XlsxFileReader::createReader($this->filepath, self::getWorksheetPath($this->index));

        try {
            while ($reader->read()) {
                if (\XMLReader::ELEMENT === $reader->nodeType && 'row' === $reader->name) {
                    yield new XlsxRow(
                        filepath: $this->filepath,
                        rowIndex: (int) $reader->getAttribute('r'),
                        outerXml: $reader->readOuterXml(),
                    );
                }
            }
        } finally {
            $reader->close();
        }
    }

    public function getRow(int $index): ?RowInterface
    {
        $reader = XlsxFileReader::createReader($this->filepath, self::getWorksheetPath($this->index));

        try {
            while ($reader->read()) {
                if (\XMLReader::ELEMENT === $reader->nodeType && 'row' === $reader->name) {
                    if ((int) $reader->getAttribute('r') === $index) {
                        return new XlsxRow(
                            filepath: $this->filepath,
                            rowIndex: (int) $reader->getAttribute('r'),
                            outerXml: $reader->readOuterXml(),
                        );
                    }
                }
            }
        } finally {
            $reader->close();
        }

        return null;
    }

    public function getCell(CoordinateInterface|string $coordinate): ?CellInterface
    {
        $reader = XlsxFileReader::createReader($this->filepath, self::getWorksheetPath($this->index));

        if (!$coordinate instanceof CoordinateInterface) {
            $coordinate = Coordinate::fromString($coordinate);
        }

        while ($reader->read()) {
            if (\XMLReader::ELEMENT === $reader->nodeType && 'c' === $reader->name && $reader->getAttribute('r') === (string) $coordinate) {
                return new XlsxCell(
                    $this->filepath,
                    $reader->readOuterXml(),
                );
            }
        }

        return null;
    }

    public function countRows(): int
    {
        $reader = XlsxFileReader::createReader($this->filepath, self::getWorksheetPath($this->index));

        $rowCount = 0;

        while ($reader->read()) {
            if (\XMLReader::ELEMENT === $reader->nodeType && 'row' === $reader->name) {
                ++$rowCount;
            }
        }

        $reader->close();

        return $rowCount;
    }

    private static function getWorksheetPath(int $sheetId): string
    {
        return sprintf(
            'worksheets/sheet%d',
            $sheetId,
        );
    }
}
