<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Helper;

use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\SheetNotFoundException;

class XlsxFileReader
{
    public static function createReader(
        string $filepath,
        string $path,
    ): \XMLReader {
        $reader = new \XMLReader();

        $xmlPath = self::buildXmlPath($filepath, $path);

        if (!@$reader->open($xmlPath)) {
            throw new SheetNotFoundException(sprintf('Could not open %s in %s', $path, $filepath));
        }

        return $reader;
    }

    private static function buildXmlPath(
        string $filepath,
        string $path,
    ): string {
        return sprintf(
            'zip://%s#xl/%s.xml',
            $filepath,
            $path,
        );
    }
}
