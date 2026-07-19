<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Integration\Spreadsheet\Helper;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\SheetNotFoundException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Helper\XlsxFileReader;

class XlsxFileReaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/' . uniqid('xlsx_helper_', true);
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

    private function createDummyXlsx(string $filename, string $internalXmlPath): string
    {
        $path = $this->tempDir . '/' . $filename;
        $zip = new \ZipArchive();

        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString($internalXmlPath, '<?xml version="1.0" encoding="UTF-8"?><root></root>');
        $zip->close();

        return $path;
    }

    public function testCreateReaderReturnsXmlReaderOnSuccess(): void
    {
        $xlsxPath = $this->createDummyXlsx('example.xlsx', 'xl/workbook.xml');

        $reader = XlsxFileReader::createReader($xlsxPath, 'workbook');

        $this->assertInstanceOf(
            \XMLReader::class,
            $reader,
            'Should return a valid XMLReader instance when the file exists.'
        );

        $reader->close();
    }

    public function testCreateReaderThrowsExceptionIfInternalFileIsMissing(): void
    {
        $xlsxPath = $this->createDummyXlsx('example.xlsx', 'xl/workbook.xml');

        $this->expectException(SheetNotFoundException::class);

        XlsxFileReader::createReader($xlsxPath, 'sheet1');
    }

    public function testCreateReaderThrowsExceptionIfArchiveDoesNotExist(): void
    {
        $this->expectException(SheetNotFoundException::class);

        XlsxFileReader::createReader($this->tempDir . '/does_not_exist.xlsx', 'workbook');
    }
}
