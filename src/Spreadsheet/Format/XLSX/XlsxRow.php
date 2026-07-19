<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CellInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\RowInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\MissingAttributeException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\ValueObject\Coordinate;

class XlsxRow implements RowInterface
{
    /**
     * @var array<string, CellInterface>|null
     */
    private ?array $cachedCells = null;

    public function __construct(
        private readonly string $filepath,
        public private(set) readonly int $rowIndex,
        private readonly string $outerXml,
    ) {}

    /**
     * @return array<string, CellInterface>
     */
    public function getCells(): array
    {
        if (null !== $this->cachedCells) {
            return $this->cachedCells;
        }

        $this->cachedCells = [];

        if (empty($this->outerXml)) {
            return $this->cachedCells;
        }

        $reader = new \XMLReader();
        $reader->XML($this->outerXml);

        while ($reader->read()) {
            if (\XMLReader::ELEMENT === $reader->nodeType && 'c' === $reader->name) {
                $coordinateStr = $reader->getAttribute('r');

                if (null === $coordinateStr) {
                    throw new MissingAttributeException();
                }

                $coordinate = Coordinate::fromString($coordinateStr);

                $this->cachedCells[$coordinate->column] = new XlsxCell(
                    $this->filepath,
                    $reader->readOuterXml(),
                );
            }
        }

        $reader->close();

        return $this->cachedCells;
    }

    public function getCell(string $columnLetter): ?CellInterface
    {
        $upperColumn = strtoupper($columnLetter);

        return $this->getCells()[$upperColumn] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->getCells());
    }
}
