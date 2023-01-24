<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ReportCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $manager,
        private TokenStorageInterface  $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onCreateAction', EventPriorities::PRE_WRITE],
        ];
    }

    public function onCreateAction(ViewEvent $event): void
    {
        $report = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($report instanceof Report and Request::METHOD_POST === $method) {
            $this->verifyUser($report);
            $this->restrictDuplicate($report);

            $report->setStatus(Report::STATUS['open']);
        }

        if ($report instanceof Report and Request::METHOD_PUT === $method) {
            $this->verifyUser($report);

            if (in_array($report->getStatus(), [Report::STATUS['validated'], Report::STATUS['rejected']]) === false) {
                throw new \Exception('Invalid status');
            }

            /* Pass status of renting to false when report is validated */
            if($report->getStatus() === Report::STATUS['validated']) {
                $renting = $report->getRenting()->setStatus(false);
                $this->manager->persist($renting);
            }
        }
    }

    public function restrictDuplicate(Report $report): void
    {
        // check if the renting has already been reported by checking is a renting appear in the table report
        $report = $this->manager->getRepository(Report::class)->findOneBy(['renting' => $report->getRenting()]);

        if ($report) {
            throw new \Exception('This renting has already been reported');
        }

        return;
    }

    public function verifyUser(Report $report): void
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user === null) {
            throw new \Exception('You must be logged in to create a report');
        }

        /** Admin bypass */
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($user !== $report->getRenting()->getClient()) {
            throw new \Exception('You can only report your own renting');
        }

        return;
    }

}
