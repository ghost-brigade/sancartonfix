<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Renting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RentingCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $manager,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onCreateAction', EventPriorities::PRE_WRITE],
        ];
    }

    public function onCreateAction(ViewEvent $event): void
    {
        $renting = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($renting instanceof Renting and Request::METHOD_POST === $method) {
            /** Prevent arnaque */
            $renting->setPrice($renting->getHousing()->getPrice());

            $this->checkHousingIsActive($renting);
            $this->checkHousingAvailability($renting);
            $this->checkHousingDateAreConsistent($renting);
            $this->payRenting($renting);
            $this->sendConfirmationEmail($renting);
        }
    }

    private function checkHousingAvailability(Renting $renting): void
    {
        $dateStart = $renting->getDateStart();
        $dateEnd = $renting->getDateEnd();
        $housing = $renting->getHousing();

        $rentings = $this->manager->getRepository(Renting::class)->findBy([
            'housing' => $housing,
            'status' => false
        ]);

        foreach ($rentings as $renting) {
            $dateStartRenting = $renting->getDateStart();
            $dateEndRenting = $renting->getDateEnd();

            if ($dateStart >= $dateStartRenting and $dateStart <= $dateEndRenting or $dateEnd >= $dateStartRenting and $dateEnd <= $dateEndRenting) {
                throw new \Exception('Housing is not available for this period');
            }
        }

        return;
    }

    private function checkHousingDateAreConsistent(Renting $renting): void
    {
        $dateStart = $renting->getDateStart();
        $dateEnd = $renting->getDateEnd();

        // if ($dateStart >= $dateEnd) {
        //     throw new \Exception('Date are not consistent');
        // }

        return;
    }

    private function checkHousingIsActive(Renting $renting): void
    {
        $housing = $renting->getHousing();

        if (!$housing->isActive()) {
            throw new \Exception('The housing is not available for rent');
        }

        return;
    }

    private function payRenting(Renting $renting) {
        $user = $this->tokenStorage->getToken()->getUser();

        $this->checkUserHasEnoughMoney($user, $renting->getPrice());
        $user->setBalance($user->getBalance() - $renting->getPrice());

        $this->manager->persist($user);
        $this->manager->flush();

        return;
    }

    private function checkUserHasEnoughMoney(User $user, float $price): void
    {
        if ($user->getBalance() < $price) {
            throw new \Exception('Vous n\'avez pas assez d\'argent sur votre compte');
        }

        return;
    }

    private function sendConfirmationEmail(Renting $renting): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from('no-reply@sancartonfix.mimso.net')
                ->to($this->tokenStorage->getToken()->getUser()->getEmail())
                ->subject('Confirmation de location')
                ->htmlTemplate('emails/confirm-renting.html.twig')
                ->context([
                    'user' => $this->tokenStorage->getToken()->getUser(),
                    'housing' => $renting->getHousing(),
                    'renting' => $renting,
                ])
            ;

            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() .'An error occured while sending the email');
        }
    }
}
