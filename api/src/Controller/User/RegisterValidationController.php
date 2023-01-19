<?php

namespace App\Controller\User;

use App\Entity\UserRegistrationToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RegisterValidationController extends AbstractController
{
    private const ENTITY        = UserRegistrationToken::class;
    private const VALIDATE_TIME = 3600;

    public function __construct(private EntityManagerInterface $manager) {}

    public function __invoke(Request $request)
    {
        $entity = $request->attributes->get('data');

        if(get_class($entity) === self::ENTITY) {

            if($entity->isActive()) {
                return $this->json(['message' => 'Your Token is already been used']);
            }

            if($entity->getCreatedAt()->getTimestamp() + self::VALIDATE_TIME < time()) {
                $this->manager->remove($entity);
                $this->manager->flush();

                return $this->json(['message' => 'Token expired'], 400);
            }

            $entity->setActive(false);
            $entity->getAccount()->setIsVerified(true);
            $this->manager->flush();

            return $this->json(['message' => 'User verified']);
        }

        return $this->json(['message' => 'Invalid token'], 400);
    }

}
