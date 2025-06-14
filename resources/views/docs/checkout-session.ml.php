@extends('layouts.docs')

@section('header')
<h1>Checkout Session Documentation</h1>
<p>Learn how to create and manage Stripe Checkout sessions for hosted payment pages</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Overview</h2>
    <p>Checkout Sessions provide a secure, hosted payment page that handles the entire checkout flow. Perfect for e-commerce and subscription billing.</p>
</section>

<section class="docs-section">
    <h2>Create Checkout Session</h2>
    <div class="method-signature">
        <pre><code>public function createCheckoutSession(array $params): \Stripe\Checkout\Session</code></pre>
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
                <td><code>$params['line_items']</code></td>
                <td>array</td>
                <td>Yes</td>
                <td>Array of line items for the checkout</td>
            </tr>
            <tr>
                <td><code>$params['mode']</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>The mode of the checkout ('payment', 'subscription', 'setup')</td>
            </tr>
            <tr>
                <td><code>$params['success_url']</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>URL to redirect after successful payment</td>
            </tr>
            <tr>
                <td><code>$params['cancel_url']</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>URL to redirect if user cancels</td>
            </tr>
        </tbody>
    </table>

    <div class="code-example">
        <h3>Basic Checkout Example</h3>
        <pre><code>$session = $checkoutService->createCheckoutSession([
    'line_items' => [
        [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'T-shirt',
                ],
                'unit_amount' => 2000,
            ],
            'quantity' => 1,
        ],
    ],
    'mode' => 'payment',
    'success_url' => 'https://yoursite.com/success',
    'cancel_url' => 'https://yoursite.com/cancel',
]);</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test - Create Session</h3>
        <form id="checkout-session-form">
            <div class="form-group">
                <label class="form-label" for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="form-input" value="Demo Product">
            </div>
            <div class="form-group">
                <label class="form-label" for="amount">Amount (cents)</label>
                <input type="number" id="amount" name="amount" class="form-input" value="2000" min="50">
            </div>
            <div class="form-group">
                <label class="form-label" for="mode">Mode</label>
                <select id="mode" name="mode" class="form-input">
                    <option value="payment">Payment</option>
                    <option value="subscription">Subscription</option>
                    <option value="setup">Setup</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="success_url">Success URL</label>
                <input type="url" id="success_url" name="success_url" class="form-input" value="http://localhost:8000/success">
            </div>
            <div class="form-group">
                <label class="form-label" for="cancel_url">Cancel URL</label>
                <input type="url" id="cancel_url" name="cancel_url" class="form-input" value="http://localhost:8000/cancel">
            </div>
            <button type="submit" class="btn btn-primary">Create Checkout Session</button>
        </form>
    </div>

    <div id="checkout-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="checkout-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Get Checkout URL Helper</h2>
    <div class="method-signature">
        <pre><code>public function getCheckoutUrl(array $params): string</code></pre>
    </div>

    <p>This helper method creates a checkout session and returns the URL directly for immediate redirection.</p>

    <div class="code-example">
        <pre><code>$checkoutUrl = $checkoutService->getCheckoutUrl([
    'line_items' => [
        [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => ['name' => 'T-shirt'],
                'unit_amount' => 2000,
            ],
            'quantity' => 1,
        ],
    ],
    'mode' => 'payment',
    'success_url' => 'https://yoursite.com/success',
    'cancel_url' => 'https://yoursite.com/cancel',
]);

// Redirect user to Stripe Checkout
header('Location: ' . $checkoutUrl);
exit;</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test - Direct Redirect</h3>
        <form id="checkout-url-form" action="/stripe/checkout-url" method="POST">
            <div class="form-group">
                <label class="form-label" for="url_product_name">Product Name</label>
                <input type="text" id="url_product_name" name="product_name" class="form-input" value="Quick Checkout Product">
            </div>
            <div class="form-group">
                <label class="form-label" for="url_amount">Amount (cents)</label>
                <input type="number" id="url_amount" name="amount" class="form-input" value="1500" min="50">
            </div>
            <button type="submit" class="btn btn-secondary">Go to Checkout (Redirect)</button>
        </form>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve Checkout Session</h2>
    <div class="method-signature">
        <pre><code>public function retrieveCheckoutSession(string $sessionId): \Stripe\Checkout\Session</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$session = $checkoutService->retrieveCheckoutSession('cs_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Checkout Sessions</h2>
    <div class="method-signature">
        <pre><code>public function listCheckoutSessions(array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$sessions = $checkoutService->listCheckoutSessions([
    'limit' => 10,
    'created' => [
        'gte' => strtotime('2023-01-01'),
    ],
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Expire Checkout Session</h2>
    <div class="method-signature">
        <pre><code>public function expireCheckoutSession(string $sessionId): \Stripe\Checkout\Session</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$expired = $checkoutService->expireCheckoutSession('cs_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Line Items</h2>
    <div class="method-signature">
        <pre><code>public function listLineItems(string $sessionId, array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$lineItems = $checkoutService->listLineItems('cs_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Validation Methods</h2>

    <div class="method-signature">
        <pre><code>public function isValidCheckoutSession(string $sessionId): bool</code></pre>
    </div>

    <div class="method-signature">
        <pre><code>public function isExpiredCheckoutSession(string $sessionId): bool</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$isValid = $checkoutService->isValidCheckoutSession('cs_1234567890');
$isExpired = $checkoutService->isExpiredCheckoutSession('cs_1234567890');

if ($isValid && !$isExpired) {
    echo "Session is active and valid";
}</code></pre>
    </div>
</section>

<script>
    document.getElementById('checkout-session-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('checkout-result-container');
        const resultOutput = document.getElementById('checkout-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/checkout-session', {
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