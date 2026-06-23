<?php
declare(strict_types=1);

namespace App\Sorter;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    
    public static function fromString(string $direction): self
    {
        $normalized = strtolower(trim($direction));
        return self::tryFrom($normalized)
            ?? throw new \InvalidArgumentException(
                sprintf("Invalid sort direction: '%s'. Allowed: 'asc', 'desc'.", $direction)
            );
    }

    public function multiplier(): int
    {
        return $this === self::ASC ? 1 : -1;
    }
}