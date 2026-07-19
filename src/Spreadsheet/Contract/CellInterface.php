<?php

declare(strict_types=1);

namespace ThePantry\LightweightSpreadsheet\Spreadsheet\Contract;

interface CellInterface
{
    public mixed $value { get; }

    public ?string $rawValue { get; }

    public ?string $type { get; }

    public CoordinateInterface $coordinate { get; }
}
