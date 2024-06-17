<?php

namespace App\Eventing\Listeners;

use App\Eventing\Events\UserCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventsListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            UserCreatedEvent::NAME => 'handleUserWasCreated'
        ];
    }

    public function handleUserWasCreated(UserCreatedEvent $event) {
        $file = fopen("confirmationTokens.txt", "w") or die("Unable to open file");

        $email = $event->getEmail();
        $token = $event->getUserConfirmationToken();

        var_dump("$email : $token");

        fwrite($file, "$email : $token");

        fclose($file);
    }
}