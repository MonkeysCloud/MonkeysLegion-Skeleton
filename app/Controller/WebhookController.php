<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Stripe\Service\ServiceContainer;
use MonkeysLegion\Template\Renderer;

/**
 * WebhookController handles Stripe webhook events with idempotency protection
 * 
 * Workflow:
 * 1. Stripe sends webhook event to /webhook/stripe endpoint
 * 2. Controller verifies the signature using Stripe's webhook secret
 * 3. Event is checked against idempotency store to prevent duplicate processing
 * 4. If not duplicate, event is processed and logged
 * 5. Event ID is stored in idempotency store to prevent future duplicates
 * 6. Success/failure response is returned to Stripe
 */
final class WebhookController
{
    private $StripeWebhook;
    private string $logFile;

    public function __construct(private Renderer $renderer)
    {
        // Initialize the Stripe webhook handler from service container
        $c = ServiceContainer::getInstance();
        $this->StripeWebhook = $c->get('webhook_controller');

        // Set up logging directory and file
        $this->logFile = base_path('var/log/stripe_webhooks.log');

        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Display webhook demo page with real-time event monitoring
     * Shows configuration instructions and live event feed
     */
    #[Route(
        methods: 'GET',
        path: '/webhook/demo',
        name: 'webhook.demo',
        summary: 'Webhook demo page',
        tags: ['Webhook']
    )]
    public function demo(): Response
    {
        // Render the demo page with webhook URL for easy configuration
        $html = $this->renderer->render('webhook/demo', [
            'title' => 'Stripe Webhook Demo',
            'webhook_url' => 'http://localhost:8000/webhook/stripe',
            'webhook_logs' => []
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    /**
     * Main webhook endpoint - receives and processes Stripe webhook events
     * 
     * Process flow:
     * 1. Extract payload and signature from request
     * 2. Verify signature using Stripe's webhook secret
     * 3. Check idempotency to prevent duplicate processing
     * 4. Process event through callback function
     * 5. Return appropriate response to Stripe
     */
    #[Route(
        methods: 'POST',
        path: '/webhook/stripe',
        name: 'webhook.stripe',
        summary: 'Handle Stripe webhooks',
        tags: ['Webhook']
    )]
    public function handleStripeWebhook(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            // Step 1: Extract raw payload and signature header
            $payload = file_get_contents('php://input');
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

            // Step 2: Pass to webhook handler which will:
            // - Verify the signature against webhook secret
            // - Check idempotency store for duplicates
            // - Parse the event data
            // - Call our processWebhookEvent callback
            $this->StripeWebhook->handle(
                $payload,
                $sigHeader,
                [$this, 'processWebhookEvent']  // Callback to process verified events
            );

            // Step 3: Return success response to Stripe
            return new Response(
                Stream::createFromString(json_encode(['status' => 'success'])),
                200,
                $headers
            );
        } catch (\InvalidArgumentException $e) {
            // Handle duplicate events (idempotency protection triggered)
            if (strpos($e->getMessage(), 'Event already processed') !== false) {
                // Extract event ID from error message for logging
                preg_match('/Event already processed: (.+)/', $e->getMessage(), $matches);
                $eventId = $matches[1] ?? 'unknown';

                // Log duplicate attempt for monitoring
                $this->logWebhookEvent([
                    'timestamp' => date('Y-m-d H:i:s'),
                    'type' => 'DUPLICATE',
                    'event_name' => 'Already Processed Event',
                    'event_id' => $eventId,
                    'message' => 'Event was already processed by the system'
                ]);

                // Return 200 OK to stop Stripe from retrying
                return new Response(
                    Stream::createFromString(json_encode([
                        'status' => 'already_processed'
                    ])),
                    200,
                    $headers
                );
            }

            // Handle other validation errors (invalid signature, etc.)
            $this->logWebhookEvent([
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'ERROR',
                'event_name' => 'Invalid Argument',
                'error' => $e->getMessage()
            ]);

            return new Response(
                Stream::createFromString(json_encode([
                    'error' => $e->getMessage()
                ])),
                400,
                $headers
            );
        } catch (\Exception $e) {
            // Handle unexpected system errors
            $this->logWebhookEvent([
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'ERROR',
                'event_name' => 'System Error',
                'error' => $e->getMessage()
            ]);

            return new Response(
                Stream::createFromString(json_encode([
                    'error' => $e->getMessage()
                ])),
                400,
                $headers
            );
        }
    }

    /**
     * Callback function to process verified webhook events
     * Called by the webhook handler after signature verification and idempotency check
     * 
     * @param array $eventData Verified event data from Stripe
     * @return bool True if processing successful
     */
    public function processWebhookEvent(array $eventData): bool
    {
        $eventId = $eventData['id'] ?? 'unknown';
        $eventType = $eventData['type'] ?? 'unknown';

        try {
            // Log successful event processing
            // The webhook handler has already verified the signature and checked idempotency
            // so we can safely process this event
            $this->logWebhookEvent([
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'SUCCESS',
                'event_name' => $this->getEventDisplayName($eventType),
                'event_id' => $eventId,
                'message' => 'Event processed successfully: ' . $this->getSuccessMessageForEvent($eventType)
            ]);

            // Here you would add your business logic based on event type
            // For example: update database, send emails, trigger workflows, etc.

            return true; // Signal successful processing
        } catch (\Exception $e) {
            // Log any errors during event processing
            $this->logWebhookEvent([
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'ERROR',
                'event_name' => $this->getEventDisplayName($eventType),
                'event_id' => $eventId,
                'error' => $e->getMessage()
            ]);

            throw $e; // Re-throw to be handled by main catch block
        }
    }

    /**
     * API endpoint to retrieve recent webhook event logs
     * Used by the demo page for real-time monitoring
     */
    #[Route(
        methods: 'GET',
        path: '/webhook/logs',
        name: 'webhook.logs',
        summary: 'Get webhook logs',
        tags: ['Webhook']
    )]
    public function getLogs(): Response
    {
        $logs = $this->getRecentWebhookLogs();

        return new Response(
            Stream::createFromString(json_encode($logs)),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Clear all webhook logs (for testing/demo purposes)
     */
    #[Route(
        methods: 'POST',
        path: '/webhook/clear-logs',
        name: 'webhook.clear.logs',
        summary: 'Clear webhook logs',
        tags: ['Webhook']
    )]
    public function clearLogs(): Response
    {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }

        return new Response(
            Stream::createFromString(json_encode(['status' => 'cleared'])),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Remove specific event from idempotency store
     * Allows re-processing of specific events for testing
     */
    #[Route(
        methods: 'DELETE',
        path: '/webhook/event/{eventId}',
        name: 'webhook.remove.event',
        summary: 'Remove specific event from store',
        tags: ['Webhook']
    )]
    public function removeEvent(string $eventId): Response
    {
        $this->StripeWebhook->removeProcessedEvent($eventId);

        return new Response(
            Stream::createFromString(json_encode(['status' => 'event_removed', 'event_id' => $eventId])),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Clear the idempotency store without clearing logs
     * Useful for testing - allows events to be processed as "new" again
     */
    #[Route(
        methods: 'POST',
        path: '/webhook/clear-store',
        name: 'webhook.clear.store',
        summary: 'Clear idempotency store only',
        tags: ['Webhook']
    )]
    public function clearIdempotencyStore(): Response
    {
        $this->StripeWebhook->clearProcessedEvents();

        return new Response(
            Stream::createFromString(json_encode(['status' => 'store_cleared'])),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Write event data to log file for monitoring and debugging
     */
    private function logWebhookEvent(array $eventData): void
    {
        $logEntry = json_encode($eventData) . "\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Retrieve and format recent webhook logs for display
     */
    private function getRecentWebhookLogs(int $limit = 20): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $logs = [];

        // Get the last N lines
        $recentLines = array_slice($lines, -$limit);

        foreach (array_reverse($recentLines) as $line) {
            $decoded = json_decode($line, true);
            if ($decoded) {
                // Adapt the log structure for the template
                $logEntry = [
                    'timestamp' => $decoded['timestamp'] ?? date('Y-m-d H:i:s'),
                    'type' => $decoded['event_name'] ?? $this->getEventDisplayName($decoded['type'] ?? 'unknown'),
                    'event_id' => $decoded['event_id'] ?? null,
                    'error' => $decoded['error'] ?? null,
                    'message' => $decoded['message'] ?? null,
                    'data' => null // Don't include full data in logs for security
                ];

                // For successful events, add a success indicator
                if (!isset($decoded['error']) && !isset($decoded['message']) && isset($decoded['event_name'])) {
                    $logEntry['message'] = 'Event processed successfully';
                }

                $logs[] = $logEntry;
            }
        }

        return $logs;
    }

    /**
     * Convert Stripe event types to human-readable display names
     */
    private function getEventDisplayName(string $eventType): string
    {
        $eventNames = [
            'payment_intent.succeeded' => 'Payment Succeeded',
            'payment_intent.payment_failed' => 'Payment Failed',
            'payment_intent.created' => 'Payment Created',
            'charge.succeeded' => 'Charge Succeeded',
            'charge.failed' => 'Charge Failed',
            'checkout.session.completed' => 'Checkout Completed',
            'setup_intent.succeeded' => 'Setup Intent Succeeded',
            'customer.created' => 'Customer Created',
            'customer.updated' => 'Customer Updated',
            'invoice.payment_succeeded' => 'Invoice Payment Succeeded',
            'invoice.payment_failed' => 'Invoice Payment Failed',
            'subscription.created' => 'Subscription Created',
            'subscription.updated' => 'Subscription Updated',
            'subscription.deleted' => 'Subscription Cancelled',
            'v1.billing.meter.no_meter_found' => 'Billing Meter Not Found'
        ];

        return $eventNames[$eventType] ?? ucwords(str_replace(['.', '_'], ' ', $eventType));
    }

    /**
     * Generate descriptive success messages for different event types
     */
    private function getSuccessMessageForEvent(string $eventType): string
    {
        switch ($eventType) {
            case 'payment_intent.succeeded':
                return 'Payment was successfully processed';
            case 'payment_intent.payment_failed':
                return 'Payment failure was properly handled';
            case 'checkout.session.completed':
                return 'Checkout session was completed successfully';
            case 'setup_intent.succeeded':
                return 'Payment method was successfully set up';
            default:
                return 'Event was processed successfully';
        }
    }
}
