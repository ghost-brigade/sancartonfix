<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;

final class UserRegistrationSendMail implements EventSubscriberInterface
{
    public function __construct(private MailerInterface $mailer) {}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['sendConfirmationEmail', EventPriorities::POST_VALIDATE],
        ];
    }

    public function sendConfirmationEmail(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($user instanceof User and Request::METHOD_POST === $method) {
            $token = uniqid() . '-' . sha1($user->getEmail());
            $expirationDate = (new \DateTime())->modify('+1 hour');

            $email = (new TemplatedEmail())
                ->from('no-reply@sancartonfix.mimso.net')
                ->to($user->getEmail())
                ->subject('Confirmation de votre compte')
                ->htmlTemplate('emails/registration.html.twig')
                ->context([
                    'user' => $user,
                    'token' => $token,
                    'expirationDate' => $expirationDate,
                ])  
            ;   
            
            $this->mailer->send($email);
        }

        return;
    }
}
