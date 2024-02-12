<?php

namespace Erla\WpToolsetFixtures\Vault;

class IdVault
{
    private array $vault;

    public function __construct() {
        $this->vault = [];
    }

    public function hasEntry(string $id): bool
    {
        return isset($this->vault[$id]);
    }

    public function get(string $id): ?string
    {
        if ($this->hasEntry($id)) {
            return $this->vault[$id];
        }
        return null;
    }

    public function set(string $id, string $value): void
    {
        $this->vault[$id] = $value;
    }
}