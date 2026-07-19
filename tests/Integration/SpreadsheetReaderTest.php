<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Integration;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\FileNotFoundException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\UnsupportedFormatException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX\XlsxSpreadsheet;
use ThePantry\LightweightSpreadsheet\SpreadsheetReader;

class SpreadsheetReaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/' . uniqid('spreadsheet_factory_', true);
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            if (false !== $files) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            rmdir($this->tempDir);
        }
    }

    private function createTempFile(string $filename): string
    {
        $path = $this->tempDir . '/' . $filename;
        touch($path);

        return $path;
    }

    public function testOpenThrowsExceptionIfFileDoesNotExist(): void
    {
        $this->expectException(FileNotFoundException::class);

        SpreadsheetReader::open($this->tempDir . '/example.xlsx');
    }

    public function testOpenThrowsExceptionOnUnsupportedFormat(): void
    {
        $csvPath = $this->createTempFile('example.csv');

        $this->expectException(UnsupportedFormatException::class);

        SpreadsheetReader::open($csvPath);
    }

    public function testOpenReturnsXlsxSpreadsheet(): void
    {
        $xlsxPath = $this->createTempFile('example.xlsx');

        $reader = SpreadsheetReader::open($xlsxPath);

        $this->assertInstanceOf(
            XlsxSpreadsheet::class,
            $reader,
            'Should return an instance of XlsxSpreadsheet for .xlsx files.'
        );
    }

    public function testOpenHandlesUppercaseExtensionsCorrectly(): void
    {
        $uppercasePath = $this->createTempFile('example.XLSX');

        $reader = SpreadsheetReader::open($uppercasePath);

        $this->assertInstanceOf(
            XlsxSpreadsheet::class,
            $reader,
            'Should be able to handle uppercase file extensions (.XLSX).'
        );
    }
}
