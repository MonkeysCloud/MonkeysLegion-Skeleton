@extends('layouts.docs')

@section('header')
<h1>Subscription Demo</h1>
<p>Test Stripe Subscription creation and cancellation</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Create Subscription Demo</h2>
    <p><strong>Note:</strong> Create recurring subscriptions. Requires Customer ID and Price ID.</p>

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
    <h2>Retrieve Subscription Demo</h2>
    <p><strong>Note:</strong> Get details of an existing subscription by ID.</p>

    <div class="example-form">
        <h3>Interactive Test - Retrieve Subscription</h3>
        <form id="retrieve-subscription-form">
            <div class="form-group">
                <label class="form-label" for="retrieve_subscription_id">Subscription ID</label>
                <input type="text" id="retrieve_subscription_id" name="subscription_id" class="form-input" placeholder="sub_...">
            </div>
            <button type="submit" class="btn btn-info">Retrieve Subscription</button>
        </form>
    </div>

    <div id="retrieve-subscription-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="retrieve-subscription-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Customer Subscriptions Demo</h2>
    <p><strong>Note:</strong> List all subscriptions for a specific customer.</p>

    <div class="example-form">
        <h3>Interactive Test - List Subscriptions</h3>
        <form id="list-subscriptions-form">
            <div class="form-group">
                <label class="form-label" for="list_customer_id">Customer ID</label>
                <input type="text" id="list_customer_id" name="customer_id" class="form-input" placeholder="cus_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="status_filter">Status Filter (optional)</label>
                <select id="status_filter" name="status" class="form-input">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="canceled">Canceled</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="trialing">Trialing</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="subscriptions_limit">Limit (max results)</label>
                <input type="number" id="subscriptions_limit" name="limit" class="form-input" value="10" min="1" max="100">
            </div>
            <button type="submit" class="btn btn-info">List Subscriptions</button>
        </form>
    </div>

    <div id="list-subscriptions-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="list-subscriptions-result-output"><code></code></pre>
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

    // Retrieve Subscription Handler
    document.getElementById('retrieve-subscription-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('retrieve-subscription-result-container');
        const resultOutput = document.getElementById('retrieve-subscription-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/subscription/retrieve', {
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

    // List Subscriptions Handler
    document.getElementById('list-subscriptions-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('list-subscriptions-result-container');
        const resultOutput = document.getElementById('list-subscriptions-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/subscription/list', {
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