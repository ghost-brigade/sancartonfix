<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordHasherSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hashUserPlainPassword', EventPriorities::POST_VALIDATE],
        ];
    }

    public function hashUserPlainPassword(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /**
         * Method Authorized [POST, PUT, PATCH] and PlainPassword not null
         */
        if ($user instanceof User and (
                Request::METHOD_POST === $method or
                Request::METHOD_PUT === $method or
                Request::METHOD_PATCH === $method
            ) and
            $user->getPlainPassword() !== null
        ) {
            $password = $this->hasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }

        return;
    }
}
