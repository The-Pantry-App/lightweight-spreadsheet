<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\SpreadsheetInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\FileNotFoundException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\UnsupportedFormatException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX\XlsxSpreadsheet;

class SpreadsheetReader
{
    /**
     * @throws FileNotFoundException
     * @throws UnsupportedFormatException
     */
    public static function open(string $filepath): SpreadsheetInterface
    {
        $realPath = realpath($filepath);

        if (!$realPath || !file_exists($realPath)) {
            throw new FileNotFoundException();
        }

        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));

        return match ($extension) {
            XlsxSpreadsheet::getExtension() => new XlsxSpreadsheet($realPath),
            default => throw new UnsupportedFormatException(),
        };
    }
}
