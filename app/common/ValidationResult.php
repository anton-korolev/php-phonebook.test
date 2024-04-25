<?php

declare(strict_types=1);

namespace common;

class ValidationResult
{
    /** @var array<string,string[]> error list (attribute => errorList) */
    protected array $errors = [];

    /**
     *
     */
    public function hasError(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return array<string,string[]> error list (attribute => errorList)
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $attribute
     * @return string[] attribute error list (attribute => errorList)
     */
    public function getAttributeErrors(string|null $attribute): array
    {
        return isset($this->errors[$attribute]) ? $this->errors[$attribute] : [];
    }

    /**
     *
     */
    public function addError(string $attribute, string $error): void
    {
        if ('' === $error) {
            return;
        }
        $this->errors[$attribute][] = $error;
    }
}
