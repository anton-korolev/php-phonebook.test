<?php

declare(strict_types=1);

namespace models;

use common\AbstractEntity;
use common\ValidationResult;

/**
 * Subscriber model.
 */
class Subscriber extends AbstractEntity
{
    protected string $phone;
    protected string $name;
    protected string $surname;

    /**
     * {@inheritdoc}
     */
    public static function attributes(): array
    {
        return ['phone', 'name', 'surname',];
    }

    /**
     * {@inheritdoc}
     */
    public static function keyAttributes(): array
    {
        return ['phone'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeValidators(): array
    {
        return [
            'phone' => $this->validatePhone(...),
            'name' => $this->validateName(...),
            'surname' => $this->validateSurname(...),
        ];
    }

    protected function validatePhone(string $attribute, mixed &$newValue, ValidationResult|null $result = null): bool
    {
        $newValue = trim((string)$newValue);
        $hasError = false;
        if (!preg_match('/^\d{10}$/', $newValue)) {
            $hasError = true;
            $result?->addError($attribute, 'Номер телефона должен состоять из 10-ти цифр.');
        }
        return !$hasError;
    }

    /**
     *
     */
    protected function validateString(
        string $attribute,
        mixed &$newValue,
        string $emptyMessage,
        ValidationResult|null $result = null
    ): bool {
        $newValue = trim($newValue);
        if (empty($newValue)) {
            $result?->addError($attribute, $emptyMessage);
        } elseif (!preg_match('/^[^<>"&\\s\\d]*$/', $newValue)) {
            $result?->addError($attribute, 'Недопустимые символы.');
        } elseif (strlen($newValue) > 100) {
            $result?->addError($attribute, 'Слишком длинное значение.');
        } else {
            return true;
        }
        return false;
    }

    protected function validateName(string $attribute, mixed &$newValue, ValidationResult|null $result = null): bool
    {
        return $this->validateString($attribute, $newValue, 'Ввведите имя абонента.', $result);
    }

    protected function validateSurname(string $attribute, mixed &$newValue, ValidationResult|null $result = null): bool
    {
        return $this->validateString($attribute, $newValue, 'Ввведите фамилию абонента.', $result);
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }
}
