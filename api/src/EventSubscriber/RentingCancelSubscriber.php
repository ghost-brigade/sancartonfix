<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Renting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;

final class RentingCancelSubscriber implements EventSubscriberInterface
{
    public const FEES = 40;

    public function __construct(
        private MailerInterface        $mailer,
        private EntityManagerInterface $manager
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onDeleteAction', EventPriorities::PRE_WRITE],
        ];
    }

    public function onDeleteAction(ViewEvent $event): void
    {
        $renting = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($renting instanceof Renting and Request::METHOD_DELETE === $method) {
            $this->restrictDeleteInPast($renting);
            $this->restrictDeleteByTwoDaysBefore($renting);
            $this->refundToClient($renting);
        }
    }

    public function restrictDeleteInPast(Renting $renting): void
    {
        $now = new \DateTime();
        $rentingDate = $renting->getDateStart();

        if ($rentingDate < $now) {
            throw new \Exception('Vous ne pouvez pas annuler une réservation dans le passé');
        }

        return;
    }

    public function restrictDeleteByTwoDaysBefore(Renting $renting): void
    {
        $now = new \DateTimeImmutable();
        $rentingDate = $renting->getDateStart();
        $minDate = $rentingDate->sub(new \DateInterval('P2D'));
        var_dump($now);
        var_dump($minDate);
        if ($now > $minDate) {
            throw new \Exception('Vous ne pouvez pas annuler une réservation moins de 2 jours avant la date de réservation');
        }

        return;
    }

    public function refundToClient(Renting $renting): void
    {
        $user = $renting->getClient();
        $amount = $renting->getPrice();

        $date = new \DateTime();
        $minDate = $renting->getDateStart()->sub(new \DateInterval('P7D'));
        $maxDate = $renting->getDateStart()->sub(new \DateInterval('P2D'));

        /**
         * If the renting is cancelled between 7 and 2 days before the renting date
         */
        if ($date->diff($minDate)->days > 2 and $date->diff($maxDate)->days <= 7) {
            $amount = $amount - ($amount * self::FEES / 100);
        }

        $user->setBalance($user->getBalance() + $amount);

        $this->manager->persist($user);
        $this->manager->flush();

        $this->sendRefundEmail($renting);
    }

    private function sendRefundEmail(Renting $renting): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from('no-reply@sancartonfix.mimso.net')
                ->to($renting->getClient()->getEmail())
                ->subject('Refund of your renting #' . $renting->getHousing()->getName())
                ->htmlTemplate('emails/refund.html.twig')
                ->context([
                    'user' => $renting->getClient(),
                    'housing' => $renting->getHousing(),
                    'renting' => $renting,
                ]);

            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . 'An error occured while sending the email');
        }
    }

}
