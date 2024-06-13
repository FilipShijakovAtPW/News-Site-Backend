<?php

namespace App\Model\Identifier;

interface IdentifierInterface
{
    public static function fromString(string $id);
    public static function generate();

    public function equals(?self $other);

    public function getId(): string;
}