<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Unit\Spreadsheet\Format\XLSX;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\MissingAttributeException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX\XlsxRow;

class XlsxRowTest extends TestCase
{
    public function testRowWithCells(): void
    {
        $xml = '<row r="1"><c r="A1" t="n"><v>10</v></c><c r="B1" t="n"><v>20</v></c></row>';
        $row = new XlsxRow('dummy.xlsx', 1, $xml);

        $this->assertSame(1, $row->rowIndex);
        $this->assertFalse($row->isEmpty());

        $cells = $row->getCells();
        $this->assertCount(2, $cells);
        $this->assertArrayHasKey('A', $cells);
        $this->assertArrayHasKey('B', $cells);

        $cellA = $row->getCell('a');
        $this->assertNotNull($cellA);
        $this->assertSame('10', $cellA->value);

        $cellB = $row->getCell('B');
        $this->assertNotNull($cellB);
        $this->assertSame('20', $cellB->value);

        $this->assertNull($row->getCell('C'));
    }

    public function testEmptyRow(): void
    {
        $xml = '<row r="2" />';
        $row = new XlsxRow('dummy.xlsx', 2, $xml);

        $this->assertSame(2, $row->rowIndex);
        $this->assertTrue($row->isEmpty());
        $this->assertEmpty($row->getCells());
        $this->assertNull($row->getCell('A'));
    }

    public function testRowThrowsExceptionOnMissingCellCoordinate(): void
    {
        $xml = '<row r="3"><c t="n"><v>42</v></c></row>';
        $row = new XlsxRow('dummy.xlsx', 3, $xml);

        $this->expectException(MissingAttributeException::class);
        $row->getCells();
    }
}
