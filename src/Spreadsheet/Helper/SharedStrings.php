<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Helper;

class SharedStrings
{
    /**
     * @var array<string, array<int, string>>
     */
    private static array $cache = [];

    /**
     * @return array<int, string>
     */
    public static function getSharedStrings(
        string $filepath,
    ): array {
        if (isset(self::$cache[$filepath])) {
            return self::$cache[$filepath];
        }

        $reader = XlsxFileReader::createReader($filepath, 'sharedStrings');

        $sharedStrings = [];

        while ($reader->read()) {
            if (\XMLReader::ELEMENT === $reader->nodeType && 'si' === $reader->name) {
                $currentString = '';

                while ($reader->read()) {
                    if (\XMLReader::END_ELEMENT === $reader->nodeType && 'si' === $reader->name) {
                        break;
                    }

                    if (\XMLReader::ELEMENT === $reader->nodeType && 't' === $reader->name) {
                        $currentString .= $reader->readString();
                    }
                }

                $sharedStrings[] = $currentString;
            }
        }

        $reader->close();

        self::$cache[$filepath] = $sharedStrings;

        return $sharedStrings;
    }

    public static function clearCache(string $filepath): void
    {
        unset(self::$cache[$filepath]);
    }
}
