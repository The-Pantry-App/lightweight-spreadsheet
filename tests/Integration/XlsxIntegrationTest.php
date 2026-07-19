<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Integration;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\SpreadsheetReader;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\SheetNotFoundException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\UnsupportedFormatException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\SheetInterface;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Contract\RowInterface;

class XlsxIntegrationTest extends TestCase
{
    private string $xlsxPath;
    private string $odsPath;

    protected function setUp(): void
    {
        $this->xlsxPath = realpath(__DIR__ . '/../Fixtures/Example.xlsx') ?: '';
        $this->odsPath = realpath(__DIR__ . '/../Fixtures/Example.ods') ?: '';

        if (empty($this->xlsxPath) || !file_exists($this->xlsxPath)) {
            $this->fail('Example.xlsx fixture not found.');
        }
        if (empty($this->odsPath) || !file_exists($this->odsPath)) {
            $this->fail('Example.ods fixture not found.');
        }
    }

    public function testOdsThrowsUnsupportedFormatException(): void
    {
        $this->expectException(UnsupportedFormatException::class);
        SpreadsheetReader::open($this->odsPath);
    }

    public function testOpenXlsxSuccessfully(): void
    {
        $spreadsheet = SpreadsheetReader::open($this->xlsxPath);
        $this->assertSame(2, $spreadsheet->countSheets());

        $sheets = iterator_to_array($spreadsheet->getSheets());
        $this->assertCount(2, $sheets);

        $sheet1 = $sheets[0];
        $this->assertInstanceOf(SheetInterface::class, $sheet1);
        $this->assertSame('Sheet 1 Name', $sheet1->name);
        $this->assertSame(1, $sheet1->index);

        $sheet2 = $sheets[1];
        $this->assertInstanceOf(SheetInterface::class, $sheet2);
        $this->assertSame('Sheet 2 Name', $sheet2->name);
        $this->assertSame(2, $sheet2->index);
    }

    public function testGetSheetByName(): void
    {
        $spreadsheet = SpreadsheetReader::open($this->xlsxPath);

        $sheet1 = $spreadsheet->getSheetByName('Sheet 1 Name');
        $this->assertInstanceOf(SheetInterface::class, $sheet1);
        $this->assertSame('Sheet 1 Name', $sheet1->name);

        $sheet2 = $spreadsheet->getSheetByName('Sheet 2 Name');
        $this->assertInstanceOf(SheetInterface::class, $sheet2);
        $this->assertSame('Sheet 2 Name', $sheet2->name);

        $this->expectException(SheetNotFoundException::class);
        $spreadsheet->getSheetByName('Non-existent Sheet');
    }

    public function testReadSheet1Content(): void
    {
        $spreadsheet = SpreadsheetReader::open($this->xlsxPath);
        $sheet = $spreadsheet->getSheetByName('Sheet 1 Name');
        $this->assertInstanceOf(SheetInterface::class, $sheet);

        $this->assertSame(2, $sheet->countRows());

        $row1 = $sheet->getRow(1);
        $this->assertNotNull($row1);
        $this->assertSame(1, $row1->rowIndex);
        $this->assertFalse($row1->isEmpty());

        $row6 = $sheet->getRow(6);
        $this->assertNotNull($row6);
        $this->assertSame(6, $row6->rowIndex);
        $this->assertFalse($row6->isEmpty());

        $rowNonExistent = $sheet->getRow(2);
        $this->assertNull($rowNonExistent);

        $cellA1 = $sheet->getCell('A1');
        $this->assertNotNull($cellA1);
        $this->assertSame('Row 1, Colum A', $cellA1->value);
        $this->assertSame('0', $cellA1->rawValue);
        $this->assertSame('s', $cellA1->type);
        $this->assertSame('A1', (string) $cellA1->coordinate);

        $cellC6 = $sheet->getCell('C6');
        $this->assertNotNull($cellC6);
        $this->assertSame('Row 6, Colum C', $cellC6->value);
        $this->assertSame('1', $cellC6->rawValue);
        $this->assertSame('s', $cellC6->type);
        $this->assertSame('C6', (string) $cellC6->coordinate);

        $cellNonExistent = $sheet->getCell('B2');
        $this->assertNull($cellNonExistent);
    }

    public function testReadSheet1RowsAndCells(): void
    {
        $spreadsheet = SpreadsheetReader::open($this->xlsxPath);
        $sheet = $spreadsheet->getSheetByName('Sheet 1 Name');
        $this->assertInstanceOf(SheetInterface::class, $sheet);

        $rows = iterator_to_array($sheet->getRows());
        $this->assertCount(2, $rows);

        /** @var RowInterface $row1 */
        $row1 = $rows[0];
        $this->assertSame(1, $row1->rowIndex);
        $cells1 = $row1->getCells();
        $this->assertCount(1, $cells1);
        $this->assertArrayHasKey('A', $cells1);
        $this->assertSame('Row 1, Colum A', $cells1['A']->value);

        $cellA = $row1->getCell('a');
        $this->assertNotNull($cellA);
        $this->assertSame('Row 1, Colum A', $cellA->value);

        $cellB = $row1->getCell('B');
        $this->assertNull($cellB);

        /** @var RowInterface $row2 */
        $row2 = $rows[1];
        $this->assertSame(6, $row2->rowIndex);
        $cells2 = $row2->getCells();
        $this->assertCount(1, $cells2);
        $this->assertArrayHasKey('C', $cells2);
        $this->assertSame('Row 6, Colum C', $cells2['C']->value);
    }

    public function testReadSheet2Content(): void
    {
        $spreadsheet = SpreadsheetReader::open($this->xlsxPath);
        $sheet = $spreadsheet->getSheetByName('Sheet 2 Name');
        $this->assertInstanceOf(SheetInterface::class, $sheet);

        $this->assertSame(1012, $sheet->countRows());

        $rows = iterator_to_array($sheet->getRows());
        $this->assertCount(1012, $rows);

        $firstRow = $rows[0];
        $this->assertSame(1, $firstRow->rowIndex);
        $firstCells = $firstRow->getCells();
        $this->assertCount(1, $firstCells);
        $this->assertArrayHasKey('A', $firstCells);
        $this->assertSame('1.23456789E8', $firstCells['A']->value);

        $lastRow = $rows[1011];
        $this->assertSame(2000, $lastRow->rowIndex);
        $lastCells = $lastRow->getCells();
        $this->assertCount(1, $lastCells);
        $this->assertArrayHasKey('A', $lastCells);
        $this->assertSame('2000.0', $lastCells['A']->value);
    }
}
