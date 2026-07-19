[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://vshymanskyy.github.io/StandWithUkraine/)

# Lightweight Spreadsheet

[![GitHub Stars](https://img.shields.io/github/stars/The-Pantry-App/lightweight-spreadsheet.svg?style=flat-square)](https://github.com/The-Pantry-App/lightweight-spreadsheet/stargazers)
[![GitHub Issues](https://img.shields.io/github/issues/The-Pantry-App/lightweight-spreadsheet.svg?style=flat-square)](https://github.com/The-Pantry-App/lightweight-spreadsheet/issues)
[![PHP Tests](https://img.shields.io/github/actions/workflow/status/The-Pantry-App/lightweight-spreadsheet/php.yml?style=flat-square)](https://github.com/The-Pantry-App/lightweight-spreadsheet/actions/workflows/php.yml)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/the-pantry/lightweight-spreadsheet.svg?style=flat-square)](https://packagist.org/packages/the-pantry/lightweight-spreadsheet)
[![Downloads](https://img.shields.io/packagist/dt/the-pantry/lightweight-spreadsheet.svg?style=flat-square)](https://packagist.org/packages/the-pantry/lightweight-spreadsheet)
[![PHP Version](https://img.shields.io/packagist/php-v/the-pantry/lightweight-spreadsheet?style=flat-square)](https://packagist.org/packages/the-pantry/lightweight-spreadsheet)

A lightweight PHP library for reading spreadsheet files with a focus on low memory usage and simplicity.

## Features

- **XLSX Support**: Read modern Excel files.
- **Generator Based**: Uses PHP Generators to iterate over sheets and rows, keeping memory usage low.
- **Simple API**: Easy to use interface for accessing data.
- **PHP 8.4+ Ready**: Built with the latest PHP features.

## Requirements

- **PHP**: ^8.4
- **Extensions**: `ext-xmlreader`, `ext-zip`

## Installation

To install, use composer:

```bash
composer require the-pantry/lightweight-spreadsheet
```

## Usage

### Opening a Spreadsheet

```php
use ThePantry\LightweightSpreadsheet\SpreadsheetReader;

$spreadsheet = SpreadsheetReader::open('path/to/spreadsheet.xlsx');
```

### Iterating through Sheets, Rows, and Cells

```php
foreach ($spreadsheet->getSheets() as $sheet) {
    echo "Sheet: " . $sheet->name . PHP_EOL;

    foreach ($sheet->getRows() as $row) {
        foreach ($row->getCells() as $cell) {
            echo "Cell " . $cell->coordinate->toString() . ": " . $cell->value . PHP_EOL;
        }
    }
}
```

### Accessing a Specific Sheet or Row

```php
$sheet = $spreadsheet->getSheetByName('Sheet1');
$row = $sheet?->getRow(1);
```

### Accessing a Specific Ceil

```php
$sheet = $spreadsheet->getSheetByName('Sheet1');
$row = $sheet?->getCeil('A1');
```

## Project Structure

- `src/`: Library source code.
  - `Spreadsheet/`: Core spreadsheet logic and formats.
  - `SpreadsheetReader.php`: Main entry point.
- `tests/`: Unit and integration tests.

## Development Scripts

The following scripts are available via Composer:

- **Run Tests**: `composer run test`
- **Static Analysis**: `composer run analyse`
- **Check Code Style**: `composer run cs-check`
- **Fix Code Style**: `composer run cs-fix`

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.