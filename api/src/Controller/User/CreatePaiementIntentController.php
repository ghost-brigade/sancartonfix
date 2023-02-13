<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreatePaiementIntentController extends AbstractController
{
    private const ENTITY = User::class;

    public function __construct(
        private EntityManagerInterface $manager
    ) {}

    public function __invoke(User $user)
    {
        $balanceStripe = $user->getBalanceStripe();

        if($balanceStripe < 10 || $balanceStripe > 1000) {
            throw new \Exception('Invalid balance, you must have between 10 and 1000 euros');
        }

        Stripe::setApiKey('sk_test_51LK2pGFyU8YibUVTNo5oceHNkoRBePrSlS7xi19vsAGjhKUbshd2PmHIrqmRWlEbyK8COTDSiCjJZB6XOQqBrkdh00iZVE26KP');

        $session = Session::create([
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data'=> [
                        'currency' => 'EUR',
                        'product_data' => [
                            'name' => "Rechargement de compte"
                        ],
                        'unit_amount' => $balanceStripe * 100
                    ]
                ]
            ],
            'mode' => 'payment',
            'success_url' => 'https://api.sancartonfix.mimso.net//success?k=' . base64_encode($this->getUser()->getId()),
            'cancel_url' => 'https://sancartonfix.mimso.net/',
            'metadata' => [
                'user_id' => $this->getUser()->getId()
            ]
        ]);

        return $this->json([
            'paymentIntent' => $session->url
        ]);
    }
}
