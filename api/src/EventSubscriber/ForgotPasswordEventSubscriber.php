<?php

namespace App\EventSubscriber;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Symfony 4.3 and inferior, use 'coop_tilleuls_forgot_password.create_token' event name
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword',
        ];
    }

    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        try {
            $email = (new TemplatedEmail())
                ->from('no-reply@sancartonfix.mimso.net')
                ->to($user->getEmail())
                ->subject('Reset your password')
                ->htmlTemplate('emails/forgot_password.html.twig')
                ->context([
                    'reset_password_url' => sprintf('https://sancartonfix.mimso.net/forgot-password/%s', $passwordToken->getToken()),
                ])
            ;

            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() .'An error occured while sending the email');
        }

    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $user->setPlainPassword($event->getPassword());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
