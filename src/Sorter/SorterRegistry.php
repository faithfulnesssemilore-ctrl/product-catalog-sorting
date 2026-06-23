<?php
declare(strict_types=1);
namespace App\Sorter;

class SorterRegistry
{
    private array $registry = [];

    public function register(string $key, SorterInterface $sorter): void
    {
        $this->registry[$key] = $sorter;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function get(string $key): SorterInterface
    {
        if (!isset($this->registry[$key])) {
            throw new \InvalidArgumentException(
                sprintf("No sorter registered for key '%s'.", $key)
            );
        }
        return $this->registry[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }
}