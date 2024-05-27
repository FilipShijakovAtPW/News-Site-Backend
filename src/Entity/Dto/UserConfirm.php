<?php

namespace App\Entity\Dto;

use App\Deserialization\DenormalizationGroups;
use Symfony\Component\Validator\Constraints as Assert;

class UserConfirm
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{7,}/",
        message: "Password must contain at least 7 characters, 1 uppercase, 1 lowercase letter and 1 number"
    )]
    private ?string $password;

    #[Assert\NotBlank]
    private ?string $repeatPassword;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(?string $repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }


}