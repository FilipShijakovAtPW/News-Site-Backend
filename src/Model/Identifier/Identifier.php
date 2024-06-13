<?php

namespace App\Model\Identifier;

use Symfony\Component\Uid\Uuid;

class Identifier implements IdentifierInterface
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new Identifier($id);
    }

    public static function generate(): Identifier
    {
        return new Identifier(Uuid::v4()->toBase58());
    }

    public function equals(?IdentifierInterface $other)
    {
        return $this->getId() === $other->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

}