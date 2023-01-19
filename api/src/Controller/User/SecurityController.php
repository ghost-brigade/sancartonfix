<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SecurityController extends AbstractController
{
    private const ENTITY        = User::class;

    public function __construct(private EntityManagerInterface $manager) {}

    public function __invoke(Request $request)
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            return $this->json($user);
        }

        return $this->json(null, 404);
    }
}