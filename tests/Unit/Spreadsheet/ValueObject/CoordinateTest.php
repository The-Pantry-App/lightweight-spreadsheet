<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Tests\Unit\Spreadsheet\ValueObject;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ThePantry\LightweightSpreadsheet\Spreadsheet\ValueObject\Coordinate;
use ThePantry\LightweightSpreadsheet\Spreadsheet\Exception\InvalidCoordinateException;

class CoordinateTest extends TestCase
{
    public function testConstructorConvertsColumnToUppercase(): void
    {
        $coordinate = new Coordinate('a', 1);

        $this->assertSame('A', $coordinate->column, 'The column should always be converted to uppercase in the constructor.');
        $this->assertSame(1, $coordinate->row);
    }

    public function testToStringReturnsCorrectFormat(): void
    {
        $coordinate = new Coordinate('B', 42);

        $this->assertSame('B42', (string) $coordinate);
    }

    public function testFromStringCreatesValidCoordinate(): void
    {
        $coordinate = Coordinate::fromString('C10');

        $this->assertSame('C', $coordinate->column);
        $this->assertSame(10, $coordinate->row);
    }

    public function testFromStringHandlesLowercaseInput(): void
    {
        $coordinate = Coordinate::fromString('aa99');

        $this->assertSame('AA', $coordinate->column);
        $this->assertSame(99, $coordinate->row);
    }

    #[DataProvider('invalidCoordinateProvider')]
    public function testFromStringThrowsExceptionOnInvalidFormat(string $invalidCoordinate): void
    {
        $this->expectException(InvalidCoordinateException::class);

        Coordinate::fromString($invalidCoordinate);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function invalidCoordinateProvider(): array
    {
        return [
            'Only numbers'               => ['123'],
            'Only letters'               => ['ABC'],
            'Number before letter'       => ['1A'],
            'With special characters'    => ['A-1'],
            'With space'                 => ['A 1'],
            'Letter at the end'          => ['A1B'],
            'Empty string'               => [''],
            'With leading whitespace'    => [' A1'],
        ];
    }
}
