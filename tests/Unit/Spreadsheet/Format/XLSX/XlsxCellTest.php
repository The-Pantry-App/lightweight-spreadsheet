<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Unit\Spreadsheet\Format\XLSX;

use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\MissingAttributeException;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Format\XLSX\XlsxCell;

class XlsxCellTest extends TestCase
{
    public function testConstructsCellCorrectly(): void
    {
        $xml = '<c r="A1" t="n"><v>42</v></c>';
        $cell = new XlsxCell('dummy.xlsx', $xml);

        $this->assertSame('42', $cell->value);
        $this->assertSame('42', $cell->rawValue);
        $this->assertSame('n', $cell->type);
        $this->assertSame('A1', (string) $cell->coordinate);
    }

    public function testConstructsCellWithNoValueElement(): void
    {
        $xml = '<c r="B2" t="n"/>';
        $cell = new XlsxCell('dummy.xlsx', $xml);

        $this->assertNull($cell->value);
        $this->assertNull($cell->rawValue);
        $this->assertSame('n', $cell->type);
        $this->assertSame('B2', (string) $cell->coordinate);
    }

    public function testConstructsCellThrowsExceptionWhenCoordinateIsMissing(): void
    {
        $xml = '<c t="n"><v>42</v></c>';

        $this->expectException(MissingAttributeException::class);
        new XlsxCell('dummy.xlsx', $xml);
    }
}
