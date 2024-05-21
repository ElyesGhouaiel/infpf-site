<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formation; // Adaptez le nom de l'entité en fonction de votre entité de produit

class PaymentController extends AbstractController
{
    
    #[Route('/payment-success', name: 'success_url')]
    public function paymentSuccess(): Response
    {
        // Traitez la réussite du paiement ici
        return new Response('Paiement réussi!');
    }

    // Route d'annulation
    #[Route('/payment-cancel', name: 'cancel_url')]
    public function paymentCancel(): Response
    {
        // Gérez l'annulation du paiement ici
        return new Response('Paiement annulé.');
    }
    
    // Route pour créer une session de paiement
    #[Route('/create-checkout-session/{productId}', name: 'create_checkout_session')]
    public function createCheckoutSession(Request $request, EntityManagerInterface $entityManager, string $productId): Response
    {
        // Récupération du produit basé sur l'ID fourni
        $product = $entityManager->getRepository(Formation::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$productId);
        }

        $stripe = new StripeClient('sk_test_51PGd7RP7ZQZW88VW2ATNYiNTS3LIiR2Eas194emcEg0wBnKl8WKAz5hjQ0N9Jq4RNe9xBERyTCeuZriLSKJyApCj00DI6TBLpi');

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getNameFormation(),
                    ],
                    'unit_amount' => $product->getPriceFormation() * 100, // Convertir le prix en centimes en euros
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }
}