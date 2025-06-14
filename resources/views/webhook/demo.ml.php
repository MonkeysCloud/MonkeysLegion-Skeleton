@extends('layouts.app')

@section('header')
<h1>Stripe Webhook Demo</h1>
<p>Monitor and test your Stripe webhook events in real-time</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Webhook Configuration</h2>
    <p>Configure your Stripe webhook endpoint in the <a href="https://dashboard.stripe.com/webhooks" target="_blank">Stripe Dashboard</a>:</p>

    <div class="method-signature">
        <pre><code>Webhook URL: {{ $webhook_url }}
Events to send: payment_intent.succeeded, payment_intent.payment_failed, checkout.session.completed, setup_intent.succeeded</code></pre>
    </div>

    <div class="example-form">
        <h3>Test Webhook</h3>
        <p>Use the Stripe CLI to test webhooks locally:</p>
        <pre><code>stripe listen --forward-to localhost:8000/webhook/stripe
stripe trigger payment_intent.succeeded</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Live Webhook Events</h2>
    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
        <button id="refresh-logs" class="btn btn-primary">Refresh Events</button>
        <button id="clear-logs" class="btn btn-secondary">Clear All Logs</button>
        <button id="clear-store" class="btn btn-warning">Reset Event Store</button>
        <div id="status" style="padding: 0.5rem; border-radius: 4px; display: none;"></div>
    </div>

    <div style="margin-bottom: 1rem;">
        <small style="color: var(--text-muted);">
            <strong>Clear All Logs:</strong> Removes log history and idempotency store<br>
            <strong>Reset Event Store:</strong> Only clears idempotency store (events will be processed as new again)
        </small>
    </div>

    <div id="webhook-logs" class="result-container">
        <h3>Recent Events</h3>
        <div id="logs-content" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--color-gray, #ddd); border-radius: 4px; padding: 1rem;">
        </div>
    </div>
</section>

<section class="docs-section">
    <h2>Event Types Handled</h2>
    <table class="params-table">
        <thead>
            <tr>
                <th>Event Type</th>
                <th>Display Name</th>
                <th>Handler</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>payment_intent.succeeded</code></td>
                <td>Payment Succeeded</td>
                <td>handlePaymentIntentSucceeded()</td>
                <td>✅ Process payment</td>
            </tr>
            <tr>
                <td><code>payment_intent.payment_failed</code></td>
                <td>Payment Failed</td>
                <td>handlePaymentIntentFailed()</td>
                <td>❌ Handle failure</td>
            </tr>
            <tr>
                <td><code>checkout.session.completed</code></td>
                <td>Checkout Completed</td>
                <td>handleCheckoutSessionCompleted()</td>
                <td>🛒 Complete order</td>
            </tr>
            <tr>
                <td><code>setup_intent.succeeded</code></td>
                <td>Setup Intent Succeeded</td>
                <td>handleSetupIntentSucceeded()</td>
                <td>🔧 Setup complete</td>
            </tr>
        </tbody>
    </table>
</section>

<section class="docs-section">
    <h2>Webhook Handler Code</h2>
    <div class="code-example">
        <h3>Basic Webhook Handler</h3>
        <pre><code>// Handle webhook in your controller
$result = $this->StripeWebhook->handle(
    $payload,
    $sigHeader,
    function($eventData) {
        // Process the verified event
        switch($eventData['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($eventData['data']['object']);
                break;
            // ... other cases
        }
        return true;
    }
);</code></pre>
    </div>
</section>

<script>
    document.getElementById('refresh-logs').addEventListener('click', refreshLogs);
    document.getElementById('clear-logs').addEventListener('click', clearLogs);
    document.getElementById('clear-store').addEventListener('click', clearStore);

    async function refreshLogs() {
        try {
            const response = await fetch('/webhook/logs');
            const logs = await response.json();
            updateLogsDisplay(logs);
            showStatus('Events refreshed', 'success');
        } catch (error) {
            showStatus('Error refreshing logs: ' + error.message, 'error');
        }
    }

    async function clearLogs() {
        if (!confirm('This will clear all webhook logs and reset the idempotency store. Are you sure?')) {
            return;
        }

        try {
            const response = await fetch('/webhook/clear-logs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                updateLogsDisplay([]);
                showStatus('All logs and store cleared successfully', 'success');
            } else {
                throw new Error('Failed to clear logs');
            }
        } catch (error) {
            showStatus('Error clearing logs: ' + error.message, 'error');
        }
    }

    async function clearStore() {
        if (!confirm('This will reset the idempotency store. Previously processed events will be treated as new. Continue?')) {
            return;
        }

        try {
            const response = await fetch('/webhook/clear-store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                showStatus('Event store reset successfully - events will be processed as new', 'success');
            } else {
                throw new Error('Failed to clear store');
            }
        } catch (error) {
            showStatus('Error clearing store: ' + error.message, 'error');
        }
    }

    function updateLogsDisplay(logs) {
        const container = document.getElementById('logs-content');

        if (!logs || logs.length === 0) {
            container.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">No webhook events received yet. Try triggering some events!</p>';
            return;
        }

        let html = '';
        for (const log of logs) {
            // Determine log type styling
            let typeColor = 'var(--color-primary)';
            let typeIcon = '📝';
            let bgColor = 'transparent';

            // Improved type detection with explicit check for SUCCESS type
            if (log.error) {
                typeColor = 'var(--color-error)';
                typeIcon = '❌';
                bgColor = 'rgba(255, 0, 0, 0.05)';
            } else if (log.type && log.type === 'SUCCESS' || log.message && log.message.includes('success')) {
                typeColor = 'var(--color-success, #28a745)';
                typeIcon = '✅';
                bgColor = 'rgba(40, 167, 69, 0.05)';
            } else if (log.message && log.message.includes('duplicate')) {
                typeColor = 'var(--color-warning, orange)';
                typeIcon = '⚠️';
                bgColor = 'rgba(255, 165, 0, 0.05)';
            } else if (log.message && log.message.includes('Unknown')) {
                typeColor = 'var(--color-warning, orange)';
                typeIcon = '❓';
                bgColor = 'rgba(255, 165, 0, 0.05)';
            }

            html += `
            <div class="webhook-log-entry" style="border-bottom: 1px solid var(--color-gray); padding: 1rem 0; background-color: ${bgColor}; border-radius: 4px; margin-bottom: 0.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span class="event-type" style="font-weight: 600; color: ${typeColor};">
                        ${typeIcon} ${log.type || 'Unknown Event'}
                    </span>
                    <span class="timestamp" style="font-size: 0.875rem; color: var(--text-muted);">
                        ${log.timestamp || new Date().toLocaleString()}
                    </span>
                </div>
                ${log.event_id ? `<div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Event ID: <code>${log.event_id}</code></div>` : ''}
                ${log.error ? `<div style="color: var(--color-error); font-weight: 500; padding: 0.5rem; background-color: rgba(255, 0, 0, 0.1); border-radius: 4px;">❌ ${log.error}</div>` : ''}
                ${log.message ? `<div style="color: var(--text-color); margin-top: 0.5rem;">${log.message}</div>` : ''}
            </div>`;
        }

        // Store current scroll position to detect if user was at top
        const wasAtTop = container.scrollTop <= 10;

        container.innerHTML = html;

        // Auto-scroll to top to show most recent events (since logs are in reverse chronological order)
        // Only auto-scroll if user was already at the top or it's the first load
        if (wasAtTop || container.scrollTop === 0) {
            container.scrollTop = 0;
        }
    }

    async function removeEvent(eventId) {
        if (!confirm(`Remove event ${eventId} from the idempotency store? This event will be processed as new if received again.`)) {
            return;
        }

        try {
            const response = await fetch(`/webhook/event/${eventId}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                showStatus(`Event ${eventId} removed from store`, 'success');
                // Refresh logs to update display
                setTimeout(refreshLogs, 500);
            } else {
                throw new Error('Failed to remove event');
            }
        } catch (error) {
            showStatus('Error removing event: ' + error.message, 'error');
        }
    }

    function showStatus(message, type) {
        const status = document.getElementById('status');
        status.textContent = message;
        status.style.display = 'block';

        // Set colors based on type
        if (type === 'success') {
            status.style.backgroundColor = 'var(--color-success, #28a745)';
            status.style.color = 'white';
        } else if (type === 'error') {
            status.style.backgroundColor = 'var(--color-error, #dc3545)';
            status.style.color = 'white';
        } else {
            status.style.backgroundColor = 'var(--color-info, #17a2b8)';
            status.style.color = 'white';
        }

        // Hide after 3 seconds
        setTimeout(() => {
            status.style.display = 'none';
        }, 3000);
    }

    // Initial load
    refreshLogs();

    // Auto-refresh every 10 seconds
    setInterval(refreshLogs, 10000);
</script>

<style>
    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background-color: var(--color-primary, #007bff);
        color: white;
    }

    .btn-secondary {
        background-color: var(--color-secondary, #6c757d);
        color: white;
    }

    .btn-warning {
        background-color: var(--color-warning, #ffc107);
        color: black;
    }

    .btn-danger {
        background-color: var(--color-error, #dc3545);
        color: white;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>
@endsection