<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * PaymentController - DÉSACTIVÉ
 * 
 * Ce contrôleur gérait les paiements Stripe mais n'est plus utilisé actuellement.
 * Le code est conservé pour une éventuelle réactivation future.
 * 
 * @deprecated Non utilisé - Stripe désactivé sur ce projet
 */
class PaymentController extends AbstractController
{
    // ============================================================
    // STRIPE DÉSACTIVÉ - Code conservé pour référence future
    // ============================================================
    // 
    // Pour réactiver Stripe :
    // 1. Ajouter la clé secrète dans .env : STRIPE_SECRET_KEY=sk_live_xxx
    // 2. Décommenter le code ci-dessous
    // 3. Ajouter l'import : use Stripe\StripeClient;
    // 4. Implémenter un webhook pour valider les paiements
    // 
    // ============================================================

    /*
    use Stripe\StripeClient;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Formation;

    #[Route('/payment-success', name: 'success_url')]
    public function paymentSuccess(): Response
    {
        return new Response('Paiement réussi!');
    }

    #[Route('/payment-cancel', name: 'cancel_url')]
    public function paymentCancel(): Response
    {
        return new Response('Paiement annulé.');
    }
    
    #[Route('/create-checkout-session/{productId}', name: 'create_checkout_session')]
    public function createCheckoutSession(Request $request, EntityManagerInterface $entityManager, string $productId): Response
    {
        $product = $entityManager->getRepository(Formation::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$productId);
        }

        // IMPORTANT: Utiliser une variable d'environnement pour la clé Stripe
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;
        if (!$stripeSecretKey) {
            throw new \RuntimeException('STRIPE_SECRET_KEY non configurée');
        }

        $stripe = new StripeClient($stripeSecretKey);

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getNameFormation(),
                    ],
                    'unit_amount' => $product->getPriceFormation() * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }
    */
}