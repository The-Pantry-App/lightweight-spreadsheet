<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Integration\Spreadsheet\Helper;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Helper\SharedStrings;

class SharedStringsTest extends TestCase
{
    private string $xlsxPath;

    protected function setUp(): void
    {
        $this->xlsxPath = realpath(__DIR__ . '/../../../Fixtures/Example.xlsx') ?: '';
        if (empty($this->xlsxPath) || !file_exists($this->xlsxPath)) {
            $this->fail('Example.xlsx fixture not found.');
        }
    }

    protected function tearDown(): void
    {
        SharedStrings::clearCache($this->xlsxPath);
    }

    public function testGetSharedStringsReadsFromXlsxAndCaches(): void
    {
        SharedStrings::clearCache($this->xlsxPath);

        $strings = SharedStrings::getSharedStrings($this->xlsxPath);

        $this->assertNotEmpty($strings);
        $this->assertSame('Row 1, Colum A', $strings[0]);
        $this->assertSame('Row 6, Colum C', $strings[1]);

        $cachedStrings = SharedStrings::getSharedStrings($this->xlsxPath);
        $this->assertSame($strings, $cachedStrings);

        SharedStrings::clearCache($this->xlsxPath);
    }
}
