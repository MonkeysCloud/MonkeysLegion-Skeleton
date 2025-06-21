<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Stripe\Client\Subscription;
use MonkeysLegion\Stripe\Service\ServiceContainer;
use MonkeysLegion\Template\Renderer;

/**
 * SubscriptionController is responsible for handling Stripe subscription-related actions.
 */
final class SubscriptionController
{
    private $SubscriptionService;

    public function __construct(private Renderer $renderer)
    {
        $this->SubscriptionService = ML_CONTAINER->get(Subscription::class);
    }

    /**
     * Create a Stripe Subscription.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/subscription',
        name: 'stripe.subscription',
        summary: 'Create Stripe Subscription',
        tags: ['Subscription']
    )]
    public function createSubscription(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $customerId = $_POST['customer_id'] ?? '';
            $priceId = $_POST['price_id'] ?? '';

            if (empty($customerId) || empty($priceId)) {
                throw new \InvalidArgumentException('Customer ID and Price ID are required');
            }

            // Build the parameters array for subscription creation
            $options = [];

            // Add trial period if provided
            if (!empty($_POST['trial_days']) && is_numeric($_POST['trial_days'])) {
                $options['trial_period_days'] = (int)$_POST['trial_days'];
            }

            // Add payment method if provided
            if (!empty($_POST['payment_method'])) {
                $options['default_payment_method'] = $_POST['payment_method'];
            }

            // Add metadata if provided
            if (!empty($_POST['metadata'])) {
                $options['metadata'] = json_decode($_POST['metadata'], true);
            }

            $subscription = $this->SubscriptionService->createSubscription($customerId, $priceId, $options);

            $responseData = [
                'success' => true,
                'subscription_id' => $subscription->id,
                'customer' => $subscription->customer,
                'status' => $subscription->status,
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'items' => $subscription->items->data
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
     * Cancel a Stripe Subscription.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/subscription/cancel',
        name: 'stripe.subscription.cancel',
        summary: 'Cancel Stripe Subscription',
        tags: ['Subscription']
    )]
    public function cancelSubscription(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $subscriptionId = $_POST['subscription_id'] ?? '';

            if (empty($subscriptionId)) {
                throw new \InvalidArgumentException('Subscription ID is required');
            }

            $options = [];

            // Add "at period end" option if provided
            if (isset($_POST['at_period_end']) && $_POST['at_period_end'] === 'true') {
                $options['at_period_end'] = true;
            }

            $subscription = $this->SubscriptionService->cancelSubscription($subscriptionId, $options);

            $responseData = [
                'success' => true,
                'subscription_id' => $subscription->id,
                'customer' => $subscription->customer,
                'status' => $subscription->status,
                'canceled_at' => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null
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
     * Retrieve a Stripe Subscription.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/subscription/retrieve',
        name: 'stripe.subscription.retrieve',
        summary: 'Retrieve Stripe Subscription',
        tags: ['Subscription']
    )]
    public function retrieveSubscription(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $subscriptionId = $_POST['subscription_id'] ?? '';

            if (empty($subscriptionId)) {
                throw new \InvalidArgumentException('Subscription ID is required');
            }

            $subscription = $this->SubscriptionService->retrieveSubscription($subscriptionId);

            $responseData = [
                'success' => true,
                'subscription_id' => $subscription->id,
                'customer' => $subscription->customer,
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                'trial_end' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                'created' => date('Y-m-d H:i:s', $subscription->created),
                'items' => $subscription->items->data
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
     * List Stripe Subscriptions.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/subscription/list',
        name: 'stripe.subscription.list',
        summary: 'List Stripe Subscriptions',
        tags: ['Subscription']
    )]
    public function listSubscriptions(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $customerId = $_POST['customer_id'] ?? '';

            if (empty($customerId)) {
                throw new \InvalidArgumentException('Customer ID is required');
            }

            $params = [];

            if (!empty($_POST['limit']) && is_numeric($_POST['limit'])) {
                $params['limit'] = (int)$_POST['limit'];
            }

            if (!empty($_POST['status'])) {
                $params['status'] = $_POST['status'];
            }

            $subscriptions = $this->SubscriptionService->listSubscriptions($customerId, $params);

            $responseData = [
                'success' => true,
                'customer_id' => $customerId,
                'subscriptions' => array_map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'status' => $sub->status,
                        'current_period_start' => date('Y-m-d H:i:s', $sub->current_period_start),
                        'current_period_end' => date('Y-m-d H:i:s', $sub->current_period_end),
                        'trial_end' => $sub->trial_end ? date('Y-m-d H:i:s', $sub->trial_end) : null,
                        'created' => date('Y-m-d H:i:s', $sub->created)
                    ];
                }, $subscriptions->data),
                'has_more' => $subscriptions->has_more
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
