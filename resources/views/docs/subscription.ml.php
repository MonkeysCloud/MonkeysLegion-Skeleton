@extends('layouts.docs')

@section('header')
<h1>Subscription Documentation</h1>
<p>Learn how to create and manage Stripe Subscriptions with the MonkeysLegion Stripe Package</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Overview</h2>
    <p>Subscriptions allow you to charge customers on a recurring basis. This API lets you create, update, and manage subscriptions for your customers.</p>
</section>

<section class="docs-section">
    <h2>Create Subscription</h2>
    <div class="method-signature">
        <pre><code>public function createSubscription(
    string $customerId, 
    string $priceId, 
    array $options = []
): \Stripe\Subscription</code></pre>
    </div>

    <table class="params-table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>$customerId</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>ID of the customer to subscribe</td>
            </tr>
            <tr>
                <td><code>$priceId</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>ID of the price to apply to the subscription</td>
            </tr>
            <tr>
                <td><code>$options</code></td>
                <td>array</td>
                <td>No</td>
                <td>Additional options for the subscription (trial_period_days, default_payment_method, etc.)</td>
            </tr>
        </tbody>
    </table>

    <div class="code-example">
        <h3>Example</h3>
        <pre><code>$subscription = $subscriptionService->createSubscription(
    'cus_1234567890',
    'price_1234567890',
    [
        'trial_period_days' => 14,
        'default_payment_method' => 'pm_1234567890'
    ]
);</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test</h3>
        <form id="subscription-form">
            <div class="form-group">
                <label class="form-label" for="customer_id">Customer ID</label>
                <input type="text" id="customer_id" name="customer_id" class="form-input" placeholder="cus_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="price_id">Price ID</label>
                <input type="text" id="price_id" name="price_id" class="form-input" placeholder="price_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="trial_days">Trial Days (optional)</label>
                <input type="number" id="trial_days" name="trial_days" class="form-input" placeholder="14">
            </div>
            <div class="form-group">
                <label class="form-label" for="payment_method">Payment Method ID (optional)</label>
                <input type="text" id="payment_method" name="payment_method" class="form-input" placeholder="pm_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="metadata">Metadata (JSON, optional)</label>
                <textarea id="metadata" name="metadata" class="form-input" placeholder='{"order_id": "12345"}'></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Subscription</button>
        </form>
    </div>

    <div id="subscription-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="subscription-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve Subscription</h2>
    <div class="method-signature">
        <pre><code>public function retrieveSubscription(string $subscriptionId, array $options = []): \Stripe\Subscription</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$subscription = $subscriptionService->retrieveSubscription('sub_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Update Subscription</h2>
    <div class="method-signature">
        <pre><code>public function updateSubscription(string $subscriptionId, array $params): \Stripe\Subscription</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$updated = $subscriptionService->updateSubscription('sub_1234567890', [
    'metadata' => ['order_id' => '12345'],
    'proration_behavior' => 'create_prorations'
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Cancel Subscription</h2>
    <div class="method-signature">
        <pre><code>public function cancelSubscription(string $subscriptionId, array $options = []): \Stripe\Subscription</code></pre>
    </div>

    <div class="code-example">
        <pre><code>// Cancel immediately
$cancelled = $subscriptionService->cancelSubscription('sub_1234567890');

// Cancel at period end
$cancelled = $subscriptionService->cancelSubscription('sub_1234567890', [
    'at_period_end' => true
]);</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test - Cancel Subscription</h3>
        <form id="cancel-subscription-form">
            <div class="form-group">
                <label class="form-label" for="subscription_id">Subscription ID</label>
                <input type="text" id="subscription_id" name="subscription_id" class="form-input" placeholder="sub_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="at_period_end">Cancel at period end?</label>
                <select id="at_period_end" name="at_period_end" class="form-input">
                    <option value="false">No (Cancel immediately)</option>
                    <option value="true">Yes (Cancel at end of billing period)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-danger">Cancel Subscription</button>
        </form>
    </div>

    <div id="cancel-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="cancel-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Subscriptions</h2>
    <div class="method-signature">
        <pre><code>public function listSubscriptions(string $customerId, array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$subscriptions = $subscriptionService->listSubscriptions('cus_1234567890', [
    'status' => 'active',
    'limit' => 10
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Resume Subscription</h2>
    <div class="method-signature">
        <pre><code>public function resumeSubscription(string $subscriptionId, array $params = []): \Stripe\Subscription</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$resumed = $subscriptionService->resumeSubscription('sub_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Search Subscriptions</h2>
    <div class="method-signature">
        <pre><code>public function searchSubscriptions(array $params): \Stripe\SearchResult</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$results = $subscriptionService->searchSubscriptions([
    'query' => 'status:"active" AND metadata["order_id"]:"12345"'
]);</code></pre>
    </div>
</section>

<script>
    document.getElementById('subscription-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('subscription-result-container');
        const resultOutput = document.getElementById('subscription-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/subscription', {
                method: 'POST',
                body: formData
            });
            const responseText = await response.text();
            const data = JSON.parse(responseText);
            resultOutput.textContent = JSON.stringify(data, null, 2);
            resultContainer.style.display = 'block';
        } catch (error) {
            resultOutput.textContent = JSON.stringify({
                error: error.message
            }, null, 2);
            resultContainer.style.display = 'block';
        }
    });

    document.getElementById('cancel-subscription-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('cancel-result-container');
        const resultOutput = document.getElementById('cancel-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/subscription/cancel', {
                method: 'POST',
                body: formData
            });
            const responseText = await response.text();
            const data = JSON.parse(responseText);
            resultOutput.textContent = JSON.stringify(data, null, 2);
            resultContainer.style.display = 'block';
        } catch (error) {
            resultOutput.textContent = JSON.stringify({
                error: error.message
            }, null, 2);
            resultContainer.style.display = 'block';
        }
    });
</script>
@endsection