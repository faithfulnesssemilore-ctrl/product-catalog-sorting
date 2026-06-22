<?php

declare(strict_types=1);

namespace App\Sorter;

use InvalidArgumentException;

class SorterRegistry
{
    /** @var array<string, SorterInterface> */
    private array $registry = [];

    /**
     * Register a sorter with a given key.
     */
    public function register(string $key, SorterInterface $sorter): void
    {
        if (empty(trim($key))) {
            throw new InvalidArgumentException('Registry key cannot be empty.');
        }
        $this->registry[$key] = $sorter;
    }

    /**
     * Retrieve a sorter by key.
     * @throws InvalidArgumentException if key not found
     */
    public function get(string $key): SorterInterface
    {
        if (!isset($this->registry[$key])) {
            throw new InvalidArgumentException(
                sprintf("No sorter registered for key '%s'.", $key)
            );
        }
        return $this->registry[$key];
    }

    /**
     * Check if a key is registered.
     */
    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }
}