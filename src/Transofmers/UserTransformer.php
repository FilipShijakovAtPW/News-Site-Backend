<?php

namespace App\Transofmers;

use App\Document\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'isConfirmed' => $user->isConfirmed(),
            'roles' => $user->getRoles(),
        ];
    }
}