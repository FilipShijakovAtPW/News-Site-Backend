<?php

namespace App\Validation\Model;

class ErrorBag
{
    private array $errors;

    private function __construct($errors)
    {
        $this->errors = $errors;
    }

    public static function fromValidationErrors($errors): self
    {
        $errorList = [];
        foreach ($errors as $item) {
            $errorList[self::getField($item)][] = $item->getMessage();
        }

        return new ErrorBag($errorList);
    }

    /**
     * @param $item
     * @return string|array
     */
    private static function getField($item)
    {
        $field = $item->getPropertyPath();
        $field = trim($field, '[]');
        $field = str_replace('][', '.', $field);

        return $field;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return sizeof($this->errors) > 0;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}