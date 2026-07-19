<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CellInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\CoordinateInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\MissingAttributeException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Helper\SharedStrings;
use ThePantry\LightweightSpreadsheet\Spreadsheet\ValueObject\Coordinate;

class XlsxCell implements CellInterface
{
    public private(set) mixed $value = null;
    public private(set) ?string $rawValue = null;
    public private(set) ?string $type = null;
    public private(set) CoordinateInterface $coordinate;

    public function __construct(
        string $filepath,
        string $outerXml,
    ) {
        $reader = new \XMLReader();
        $reader->XML($outerXml);

        $reader->read();

        $this->type = (string) $reader->getAttribute('t');

        $innerReader = new \XMLReader();
        $innerReader->XML($reader->readOuterXml());

        while ($innerReader->read()) {
            if (\XMLReader::ELEMENT === $innerReader->nodeType && 'v' === $innerReader->name) {
                $this->value = $innerReader->readString();
                $this->rawValue = $innerReader->readString();
            }

            if (\XMLReader::ELEMENT === $reader->nodeType && 'c' === $reader->name) {
                $coordinateStr = $reader->getAttribute('r');

                if (null === $coordinateStr) {
                    throw new MissingAttributeException();
                }

                $this->coordinate = Coordinate::fromString($coordinateStr);
            }
        }

        if ('s' === $this->type) {
            $this->value = SharedStrings::getSharedStrings($filepath)[(int) $this->rawValue] ?? null;
        }
    }
}
