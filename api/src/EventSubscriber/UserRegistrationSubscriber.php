<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Entity\UserRegistrationToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;

final class UserRegistrationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $manager
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistration', EventPriorities::POST_WRITE],
        ];
    }

    public function userRegistration(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($user instanceof User and Request::METHOD_POST === $method) {
            $token = $this->generateRegistrationToken($user);
            
            $this->sendConfirmationEmail($user, $token);
            $this->insertRegistrationToken($user, $token);
        }
    }

    private function sendConfirmationEmail($user, $token): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from('no-reply@sancartonfix.mimso.net')
                ->to($user->getEmail())
                ->subject('Confirmation de votre compte')
                ->htmlTemplate('emails/registration.html.twig')
                ->context([
                    'user' => $user,
                    'token' => $token,
                ])  
            ;
            
            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new \Exception('Impossible d\'envoyer le mail de confirmation, veuillez ré-essayer plus tard.');
        }
    }

    private function insertRegistrationToken($user, $token) {
        try {
            $userRegistrationToken = new UserRegistrationToken();
            $userRegistrationToken->setToken($token);
            $userRegistrationToken->setAccount($user);
           
            $this->manager->persist($userRegistrationToken);
            $this->manager->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' Une erreur est survenue lors de la génération du token, veuillez ré-essayer plus tard.');
        }
    }

    private function generateRegistrationToken($user) {
        return uniqid() . '-' . sha1($user->getEmail());
    }
}
