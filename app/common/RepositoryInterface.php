<?php

declare(strict_types=1);

namespace common;

/**
 * Entity repository interface.
 *
 * Note that this interface was created for demonstration purposes only. In a real project, an
 * abstract repository class would be a better choice.
 *
 * @template T of AbstractEntity
 */
interface RepositoryInterface
{
    /**
     * Returns the number of Entitys.
     *
     * @return int the number of Entitys.
     */
    public function count(): int;

    /**
     * Returns a slice of the Entity list as an array, indexed by ID.
     *
     * Note, the values are returned as is and are not validated.
     *
     * @param int $offset if is non-negative, the sequence will start at that offset in the Entity list;
     * if is negative, the sequence will start that far from the end of the Entity list.
     * @param int $length if is positive, then the sequence will have up to that many elements in it;
     * if is negative then the sequence will stop that many elements from the end of the Entity list.
     *
     * @return array<string,array<string,mixed>> Entity list (ID => [attribute => value]).
     */
    public function selectPage(int $offset, int $length): array;

    /**
     * Returns the Entity corresponding to the ID.
     *
     * @param string $id an ID to search for.
     *
     * @return T|null Entity or `null` if no ID is found.
     *
     * @throws Exception if incorrect data has been retrieved from the repository.
     */
    public function find(string $id): AbstractEntity|null;

    /**
     * Adds a new Entity to the repository.
     *
     * @param T $entity Entity to be added.
     *
     * @return bool `true` if the insertion passed without error, or `false` if the Entity already exists.
     *
     * @throws Exception if there is an internal repository error.
     */
    public function insert(AbstractEntity $entity): bool;

    /**
     * Updates the Entity in the repository.
     *
     * @param string $id ID of the Entity to be updated.
     *
     * @param T $entity Entity to be updates.
     *
     * @return bool `true` if the update passed without error, or `false` if no Entity is found.
     *
     * @throws Exception if there is an internal repository error.
     */
    public function update(string $id, AbstractEntity $entity): bool;

    /**
     * Deletes the Entity from the repository.
     *
     * @param string $id ID of the Entity to be deleted.
     *
     * @return bool `true` if the deletion passed without error, or `false` if no Entity is found.
     *
     * @throws Exception if there is an internal repository error.
     */
    public function delete(string $id): bool;
}
