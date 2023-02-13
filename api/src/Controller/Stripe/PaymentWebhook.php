<?php

namespace App\Controller\Stripe;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentWebhook extends AbstractController {

    #[Route('/stripe/webhook', name: 'stripe_webhook')]
    public function webhook(UserRepository $userRepository, EntityManagerInterface $entityManager) {
        $endpoint_secret = 'whsec_eef90205102a1c1951c8d1e886219d5e9c20c1b2664227291705feec9742ce69';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return new Response('Payment error', 400, [
                'Content-Type' => 'text/plain'
            ]);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Payment error', 400, [
                'Content-Type' => 'text/plain'
            ]);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                $user = $userRepository->findOneBy(['id' => $paymentIntent['metadata']['user_id']]);

                if($user) {
                    $user->setBalance($user->getBalance() + $paymentIntent->amount);
                    $entityManager->persist($user);
                    $entityManager->flush();
                }

                return new Response('Payment Succeeded', 200, [
                    'Content-Type' => 'text/plain'
                ]);

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return new Response('Payment success', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
}



