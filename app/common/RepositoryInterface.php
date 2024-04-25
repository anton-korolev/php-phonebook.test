<?php

declare(strict_types=1);

namespace common;

interface RepositoryInterface
{
    public function find(string $id): AbstractEntity|null;

    public function insert(array $values, ValidationResult|null $result = null): AbstractEntity|null;

    public function update(array $values, ValidationResult|null $result = null): AbstractEntity|null;

    public function delete(string $id): bool;
}
