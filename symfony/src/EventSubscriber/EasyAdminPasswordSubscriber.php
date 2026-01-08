<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class EasyAdminPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {}

    public static function getSubscribedEvents(): array {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['hashPassword'],
        ];
    }

    public function hashPassword(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof User)) {
            return;
        }

        $plainPassword = $entity->getPlainPassword();

        if(!empty($plainPassword)) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $entity->setPassword($hashedPassword);
        }
    }
}
