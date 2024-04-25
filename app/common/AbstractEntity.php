<?php

declare(strict_types=1);

namespace common;

abstract class AbstractEntity
{
    private function __construct()
    {
    }

    /**
     * Returns a list of attributes.
     *
     * @return string[] attribute list.
     */
    abstract public static function attributes(): array;

    /**
     * Returns a list of attribute validators.
     *
     * All validators must have the following specification:
     * ```php
     * \Closure(string $attribute, mixed &$newValue, ValidationResult|null $result = null): bool;
     * ```
     * And return `true` if the validation passed without error, or `false` otherwise.
     *
     * @return array<string,\Closure(string $attribute, mixed &$newValue, ValidationResult|null $result = null): bool>
     * attribute validator list.
     */
    abstract public function attributeValidators(): array;

    /**
     * Validates and sets new attribute values.
     *
     * @param array<string,mixed> $newValues new attribute values.
     */
    public function setAttributes(array $newValues, ValidationResult|null $result = null): bool
    {
        $newValues = array_intersect_key($newValues, array_flip($this->attributes()));
        $validators = $this->attributeValidators();

        $hasError = false;
        foreach ($newValues as $attribute => $newValue) {
            $hasError = !$validators[$attribute]($attribute, $newValue, $result) || $hasError;
        }
        if ($hasError) {
            return false;
        }

        foreach ($newValues as $attribute => $newValue) {
            $this->$attribute = $newValue;
        }

        return true;
    }

    /**
     * Returns a list of valid attribute names.
     *
     * Only attribute names listed at `attributeList()` are returned.
     *
     * @param string[]|null $attributes list of attribute names to check.
     * Defaults to null, meaning all attribute names listed in `attributeList()` will be returned.
     *
     * @return string[] a list of valid attribute names.
     */
    protected static function normalizeAttributeList(array|null $attributes): array
    {
        return (null === $attributes)
            ? static::attributes()
            : array_intersect(static::attributes(), $attributes);
    }

    /**
     * Returns the attribute values.
     *
     * Incorrect attribute names will be skipped.
     *
     * @param string[]|null $attributes list of attributes whose value needs to be returned.
     * Defaults to null, meaning all attributes listed in `attributeList()` will be returned.
     * If it is an array, only the attributes in the array will be returned.
     *
     * @return array<string,mixed> attribute values (attribute => value).
     *
     * @see internalGetAttributes()
     */
    public function getAttributes(array|null $attributes = null): array
    {
        $attributes = $this->normalizeAttributeList($attributes);

        $values = [];

        foreach ($attributes as $attribute) {
            $values[$attribute] = $this->$attribute;
        }

        return $values;
    }

    /**
     * Internal factory method to create an instance of Entity from an associative array
     * (attribute => value).
     *
     * Missing attribute values will be filled with `null`.
     *
     * @param array<string,mixed> $values associative array of attribute values (attribute => value).
     * @param ValidationResult $result storage of validation errors.
     *
     * @return static|null a new instance of Entity if the record creation passed
     * without errors, or `null` otherwise.
     */
    public static function createFromArray(array $values, ValidationResult|null $result = null): static|null
    {
        $values = array_merge(array_fill_keys(static::attributes(), null), $values);
        $newInstance = new static();
        if ($newInstance->setAttributes($values, $result)) {
            return $newInstance;
        }
        return null;
    }
}
