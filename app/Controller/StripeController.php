<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Stripe\Service\ServiceContainer;
use MonkeysLegion\Template\Renderer;

/**
 * StripeController is responsible for handling Stripe-related actions.
 */
final class StripeController
{
    private $StripeGateway;
    private $SetupIntentService;
    private $CheckoutSessionService;

    public function __construct(private Renderer $renderer)
    {
        $c = ServiceContainer::getInstance();
        $this->StripeGateway = $c->get('StripeGateway');
        $this->SetupIntentService = $c->get('SetupIntentService');
        $this->CheckoutSessionService = $c->get('CheckoutSessionService');
    }

    /**
     * Create a Stripe PaymentIntent and return the client secret.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/payment-intent',
        name: 'stripe.payment.intent',
        summary: 'Create Stripe PaymentIntent',
        tags: ['Payment']
    )]
    public function createPaymentIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0
                ? (int)$_POST['amount']
                : 1000;

            $currency = isset($_POST['currency']) && preg_match('/^[a-zA-Z]{3}$/', $_POST['currency'])
                ? strtolower($_POST['currency'])
                : 'usd';

            $paymentIntent = $this->StripeGateway->createPaymentIntent($amount, $currency);

            $responseData = [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Create a Stripe SetupIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/setup-intent',
        name: 'stripe.setup.intent',
        summary: 'Create Stripe SetupIntent',
        tags: ['Payment']
    )]
    public function createSetupIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            // Build the parameters array for SetupIntent creation
            $params = [];

            // Add usage if provided
            if (!empty($_POST['usage'])) {
                $params['usage'] = $_POST['usage'];
            } else {
                $params['usage'] = 'off_session'; // Default value
            }

            // The service will automatically add payment_method_types if not provided

            $setupIntent = $this->SetupIntentService->createSetupIntent($params);

            $responseData = [
                'success' => true,
                'client_secret' => $setupIntent->client_secret,
                'setup_intent_id' => $setupIntent->id,
                'usage' => $setupIntent->usage,
                'status' => $setupIntent->status,
                'payment_method_types' => $setupIntent->payment_method_types
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Create a Stripe Checkout Session.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-session',
        name: 'stripe.checkout.session',
        summary: 'Create Stripe Checkout Session',
        tags: ['Payment']
    )]
    public function createCheckoutSession(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $mode = $_POST['mode'] ?? 'payment';
            $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? (int)$_POST['amount'] : 2000;
            $product_name = $_POST['product_name'] ?? 'Demo Product';
            $success_url = $_POST['success_url'] ?? 'http://localhost:8000/success';
            $cancel_url = $_POST['cancel_url'] ?? 'http://localhost:8000/cancel';

            $params = [
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $product_name,
                            ],
                            'unit_amount' => $amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => $mode,
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
            ];

            $session = $this->CheckoutSessionService->createCheckoutSession($params);

            $responseData = [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
                'mode' => $session->mode,
                'amount_total' => $session->amount_total
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Get Checkout URL and redirect.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-url',
        name: 'stripe.checkout.url',
        summary: 'Get Stripe Checkout URL and redirect',
        tags: ['Payment']
    )]
    public function getCheckoutUrl(): Response
    {
        try {
            $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? (int)$_POST['amount'] : 2000;
            $product_name = $_POST['product_name'] ?? 'Demo Product';

            $params = [
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $product_name,
                            ],
                            'unit_amount' => $amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => 'http://localhost:8000/success',
                'cancel_url' => 'http://localhost:8000/cancel',
            ];

            $checkoutUrl = $this->CheckoutSessionService->getCheckoutUrl($params);

            return new Response(
                Stream::createFromString(''),
                302,
                ['Location' => $checkoutUrl]
            );
        } catch (\Exception $e) {
            return new Response(
                Stream::createFromString(json_encode(['error' => $e->getMessage()])),
                400,
                ['Content-Type' => 'application/json']
            );
        }
    }
}
