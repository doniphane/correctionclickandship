<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/stripe', name: 'stripe_')]
final class StripeController extends AbstractController
{

    #[Route('/checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, ProductRepository $productRepository, EntityManagerInterface $em): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $cart = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $line_items = [];
        $totalAmount = 0;

        foreach ($cart as $item) {
            $product = $productRepository->find($item['productId']);

            if (!$product) {
                return $this->json(['error' => 'Product not found: ' . $item['productId']], 404);
            }

            $priceInCents = (int) ((float) $product->getPrice() * 100);
            $totalAmount += $priceInCents * $item['quantity'];

            // Create Stripe Product and Price if they don't exist
            if (!$product->getStripePriceId()) {
                try {
                    $stripeProduct = StripeProduct::create([
                        'name' => $product->getName(),
                    ]);

                    $stripePrice = StripePrice::create([
                        'product' => $stripeProduct->id,
                        'unit_amount' => $priceInCents,
                        'currency' => 'eur',
                    ]);

                    $product->setStripePriceId($stripePrice->id);
                    $em->flush();
                } catch (\Exception $e) {
                    return $this->json(['error' => 'Stripe API error: ' . $e->getMessage()], 500);
                }
            }

            $line_items[] = [
                'price' => $product->getStripePriceId(),
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => $_ENV['FRONTEND_URL'] . 'success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $_ENV['FRONTEND_URL'] . 'cancel',
            ]);

            return $this->json([
                'id' => $checkout_session->id,
                'url' => $checkout_session->url
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/success', name: 'stripe_success', methods: ['GET'])]
    public function success(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): JsonResponse
    {

        $session_id = $request->query->get('session_id');
        try {
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $session = Session::retrieve($session_id);
            $line_items = Session::allLineItems($session_id);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $order = new Order();
        $order->setStripeId($session_id);
        $order->setTotal($session->amount_total / 100);
        $order->setOwner($this->getUser());
        $entityManager->persist($order);

        $orderLines = $line_items->data;
        foreach ($orderLines as $orderLine) {
            $product = $productRepository->findOneBy(['stripePriceId' => $orderLine->price->id]);
            $orderLineEntity = new OrderLine();
            $orderLineEntity->setOrderId($order);
            $orderLineEntity->setProduct($product);
            $orderLineEntity->setPrice($orderLine->price->unit_amount / 100);
            $orderLineEntity->setQuantity($orderLine->quantity);
            $entityManager->persist($orderLineEntity);
        }
        $entityManager->flush();

        return $this->json(['message' => 'Payment successful']);
    }
}