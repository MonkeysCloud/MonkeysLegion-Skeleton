<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Stripe\Client\CheckoutSession;
use MonkeysLegion\Stripe\Client\SetupIntentService;
use MonkeysLegion\Stripe\Client\StripeGateway;
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
        $this->StripeGateway = ML_CONTAINER->get(StripeGateway::class);
        $this->SetupIntentService = ML_CONTAINER->get(SetupIntentService::class);
        $this->CheckoutSessionService = ML_CONTAINER->get(CheckoutSession::class);
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

    /**
     * Retrieve a Stripe Checkout Session.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-session/retrieve',
        name: 'stripe.checkout.session.retrieve',
        summary: 'Retrieve Stripe Checkout Session',
        tags: ['Payment']
    )]
    public function retrieveCheckoutSession(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $sessionId = $_POST['session_id'] ?? '';

            if (empty($sessionId)) {
                throw new \InvalidArgumentException('Session ID is required');
            }

            $session = $this->CheckoutSessionService->retrieveCheckoutSession($sessionId);

            $responseData = [
                'success' => true,
                'session_id' => $session->id,
                'status' => $session->status,
                'mode' => $session->mode,
                'url' => $session->url,
                'amount_total' => $session->amount_total,
                'currency' => $session->currency,
                'customer_email' => $session->customer_email,
                'payment_status' => $session->payment_status,
                'created' => date('Y-m-d H:i:s', $session->created)
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
     * List Stripe Checkout Sessions.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-session/list',
        name: 'stripe.checkout.session.list',
        summary: 'List Stripe Checkout Sessions',
        tags: ['Payment']
    )]
    public function listCheckoutSessions(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $params = [];

            // Add limit if provided
            if (!empty($_POST['limit']) && is_numeric($_POST['limit'])) {
                $params['limit'] = (int)$_POST['limit'];
            }

            // Add created filter if provided
            if (!empty($_POST['created_gte'])) {
                $timestamp = strtotime($_POST['created_gte']);
                if ($timestamp) {
                    $params['created'] = ['gte' => $timestamp];
                }
            }

            $sessions = $this->CheckoutSessionService->listCheckoutSessions($params);

            $responseData = [
                'success' => true,
                'sessions' => array_map(function ($session) {
                    return [
                        'id' => $session->id,
                        'status' => $session->status,
                        'mode' => $session->mode,
                        'amount_total' => $session->amount_total,
                        'currency' => $session->currency,
                        'payment_status' => $session->payment_status,
                        'created' => date('Y-m-d H:i:s', $session->created)
                    ];
                }, $sessions->data),
                'has_more' => $sessions->has_more
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
     * Expire a Stripe Checkout Session.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-session/expire',
        name: 'stripe.checkout.session.expire',
        summary: 'Expire Stripe Checkout Session',
        tags: ['Payment']
    )]
    public function expireCheckoutSession(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $sessionId = $_POST['session_id'] ?? '';

            if (empty($sessionId)) {
                throw new \InvalidArgumentException('Session ID is required');
            }

            $session = $this->CheckoutSessionService->expireCheckoutSession($sessionId);

            $responseData = [
                'success' => true,
                'session_id' => $session->id,
                'status' => $session->status,
                'expires_at' => $session->expires_at ? date('Y-m-d H:i:s', $session->expires_at) : null
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
     * List Line Items from a Checkout Session.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/checkout-session/line-items',
        name: 'stripe.checkout.session.line.items',
        summary: 'List Checkout Session Line Items',
        tags: ['Payment']
    )]
    public function listCheckoutSessionLineItems(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $sessionId = $_POST['session_id'] ?? '';

            if (empty($sessionId)) {
                throw new \InvalidArgumentException('Session ID is required');
            }

            $lineItems = $this->CheckoutSessionService->listLineItems($sessionId);

            $responseData = [
                'success' => true,
                'session_id' => $sessionId,
                'line_items' => array_map(function ($item) {
                    return [
                        'id' => $item->id,
                        'description' => $item->description,
                        'amount_total' => $item->amount_total,
                        'amount_subtotal' => $item->amount_subtotal,
                        'currency' => $item->currency,
                        'quantity' => $item->quantity,
                        'price' => $item->price ? [
                            'id' => $item->price->id,
                            'unit_amount' => $item->price->unit_amount,
                            'currency' => $item->price->currency
                        ] : null
                    ];
                }, $lineItems->data),
                'has_more' => $lineItems->has_more
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
     * Retrieve a Stripe PaymentIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/payment-intent/retrieve',
        name: 'stripe.payment.intent.retrieve',
        summary: 'Retrieve Stripe PaymentIntent',
        tags: ['Payment']
    )]
    public function retrievePaymentIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $paymentIntentId = $_POST['payment_intent_id'] ?? '';

            if (empty($paymentIntentId)) {
                throw new \InvalidArgumentException('PaymentIntent ID is required');
            }

            $paymentIntent = $this->StripeGateway->retrievePaymentIntent($paymentIntentId);

            $responseData = [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
                'client_secret' => $paymentIntent->client_secret,
                'created' => date('Y-m-d H:i:s', $paymentIntent->created)
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
     * Confirm a Stripe PaymentIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/payment-intent/confirm',
        name: 'stripe.payment.intent.confirm',
        summary: 'Confirm Stripe PaymentIntent',
        tags: ['Payment']
    )]
    public function confirmPaymentIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $paymentIntentId = $_POST['payment_intent_id'] ?? '';
            $paymentMethod = $_POST['payment_method'] ?? '';

            if (empty($paymentIntentId)) {
                throw new \InvalidArgumentException('PaymentIntent ID is required');
            }

            $options = [];
            if (!empty($paymentMethod)) {
                $options['payment_method'] = $paymentMethod;
            }

            $paymentIntent = $this->StripeGateway->confirmPaymentIntent($paymentIntentId, $options);

            $responseData = [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
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
     * Cancel a Stripe PaymentIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/payment-intent/cancel',
        name: 'stripe.payment.intent.cancel',
        summary: 'Cancel Stripe PaymentIntent',
        tags: ['Payment']
    )]
    public function cancelPaymentIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $paymentIntentId = $_POST['payment_intent_id'] ?? '';

            if (empty($paymentIntentId)) {
                throw new \InvalidArgumentException('PaymentIntent ID is required');
            }

            $paymentIntent = $this->StripeGateway->cancelPaymentIntent($paymentIntentId);

            $responseData = [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'cancellation_reason' => $paymentIntent->cancellation_reason
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
     * List Stripe PaymentIntents.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/payment-intent/list',
        name: 'stripe.payment.intent.list',
        summary: 'List Stripe PaymentIntents',
        tags: ['Payment']
    )]
    public function listPaymentIntents(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $params = [];

            if (!empty($_POST['limit']) && is_numeric($_POST['limit'])) {
                $params['limit'] = (int)$_POST['limit'];
            }

            if (!empty($_POST['customer_id'])) {
                $params['customer'] = $_POST['customer_id'];
            }

            $paymentIntents = $this->StripeGateway->listPaymentIntent($params);

            $responseData = [
                'success' => true,
                'payment_intents' => array_map(function ($pi) {
                    return [
                        'id' => $pi->id,
                        'status' => $pi->status,
                        'amount' => $pi->amount,
                        'currency' => $pi->currency,
                        'created' => date('Y-m-d H:i:s', $pi->created)
                    ];
                }, $paymentIntents->data),
                'has_more' => $paymentIntents->has_more
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
     * Retrieve a Stripe SetupIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/setup-intent/retrieve',
        name: 'stripe.setup.intent.retrieve',
        summary: 'Retrieve Stripe SetupIntent',
        tags: ['Payment']
    )]
    public function retrieveSetupIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $setupIntentId = $_POST['setup_intent_id'] ?? '';

            if (empty($setupIntentId)) {
                throw new \InvalidArgumentException('SetupIntent ID is required');
            }

            $setupIntent = $this->SetupIntentService->retrieveSetupIntent($setupIntentId);

            $responseData = [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'status' => $setupIntent->status,
                'usage' => $setupIntent->usage,
                'payment_method' => $setupIntent->payment_method,
                'client_secret' => $setupIntent->client_secret,
                'created' => date('Y-m-d H:i:s', $setupIntent->created)
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
     * Confirm a Stripe SetupIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/setup-intent/confirm',
        name: 'stripe.setup.intent.confirm',
        summary: 'Confirm Stripe SetupIntent',
        tags: ['Payment']
    )]
    public function confirmSetupIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $setupIntentId = $_POST['setup_intent_id'] ?? '';
            $paymentMethod = $_POST['payment_method'] ?? '';

            if (empty($setupIntentId)) {
                throw new \InvalidArgumentException('SetupIntent ID is required');
            }

            $params = [];
            if (!empty($paymentMethod)) {
                $params['payment_method'] = $paymentMethod;
            }

            $setupIntent = $this->SetupIntentService->confirmSetupIntent($setupIntentId, $params);

            $responseData = [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'status' => $setupIntent->status,
                'payment_method' => $setupIntent->payment_method
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
     * Cancel a Stripe SetupIntent.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/setup-intent/cancel',
        name: 'stripe.setup.intent.cancel',
        summary: 'Cancel Stripe SetupIntent',
        tags: ['Payment']
    )]
    public function cancelSetupIntent(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $setupIntentId = $_POST['setup_intent_id'] ?? '';

            if (empty($setupIntentId)) {
                throw new \InvalidArgumentException('SetupIntent ID is required');
            }

            $setupIntent = $this->SetupIntentService->cancelSetupIntent($setupIntentId);

            $responseData = [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'status' => $setupIntent->status,
                'cancellation_reason' => $setupIntent->cancellation_reason
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
     * List Stripe SetupIntents.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/setup-intent/list',
        name: 'stripe.setup.intent.list',
        summary: 'List Stripe SetupIntents',
        tags: ['Payment']
    )]
    public function listSetupIntents(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $params = [];

            if (!empty($_POST['limit']) && is_numeric($_POST['limit'])) {
                $params['limit'] = (int)$_POST['limit'];
            }

            if (!empty($_POST['customer_id'])) {
                $params['customer'] = $_POST['customer_id'];
            }

            $setupIntents = $this->SetupIntentService->listSetupIntents($params);

            $responseData = [
                'success' => true,
                'setup_intents' => array_map(function ($si) {
                    return [
                        'id' => $si->id,
                        'status' => $si->status,
                        'usage' => $si->usage,
                        'payment_method' => $si->payment_method,
                        'created' => date('Y-m-d H:i:s', $si->created)
                    ];
                }, $setupIntents->data),
                'has_more' => $setupIntents->has_more
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
}
