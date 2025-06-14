@extends('layouts.docs')

@section('header')
<h1>PaymentIntent Documentation</h1>
<p>Learn how to create and manage PaymentIntents with the MonkeysLegion Stripe Package</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Overview</h2>
    <p>PaymentIntents are used to handle the payment process from start to finish. They track the payment's lifecycle and handle authentication when required.</p>
</section>

<section class="docs-section">
    <h2>Create PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function createPaymentIntent(
    int $amount, 
    string $currency = 'usd', 
    bool $automatic_payment_methods = true
): \Stripe\PaymentIntent</code></pre>
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
                <td><code>$amount</code></td>
                <td>int</td>
                <td>Yes</td>
                <td>Amount in smallest currency unit</td>
            </tr>
            <tr>
                <td><code>$currency</code></td>
                <td>string</td>
                <td>No</td>
                <td>Three-letter ISO currency code (default: 'usd')</td>
            </tr>
            <tr>
                <td><code>$automatic_payment_methods</code></td>
                <td>bool</td>
                <td>No</td>
                <td>Enable automatic payment methods (default: true)</td>
            </tr>
        </tbody>
    </table>

    <div class="code-example">
        <h3>Example</h3>
        <pre><code>$paymentIntent = $stripeGateway->createPaymentIntent(1000, 'usd', true);
echo $paymentIntent->client_secret;</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test</h3>
        <form id="payment-intent-form">
            <div class="form-group">
                <label class="form-label" for="amount">Amount (cents)</label>
                <input type="number" id="amount" name="amount" class="form-input" value="1000" min="50">
            </div>
            <div class="form-group">
                <label class="form-label" for="currency">Currency</label>
                <select id="currency" name="currency" class="form-input">
                    <option value="usd">USD</option>
                    <option value="eur">EUR</option>
                    <option value="gbp">GBP</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create PaymentIntent</button>
        </form>
    </div>

    <div id="result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function retrievePaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$paymentIntent = $stripeGateway->retrievePaymentIntent('pi_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Confirm PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function confirmPaymentIntent(string $paymentIntentId, array $options = []): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$confirmed = $stripeGateway->confirmPaymentIntent('pi_1234567890', [
    'payment_method' => 'pm_card_visa'
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Cancel PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function cancelPaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$cancelled = $stripeGateway->cancelPaymentIntent('pi_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Capture PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function capturePaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$captured = $stripeGateway->capturePaymentIntent('pi_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Refund PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function refundPaymentIntent(string $paymentIntentId, array $options = []): \Stripe\Refund</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$refund = $stripeGateway->refundPaymentIntent('pi_1234567890', [
    'amount' => 500 // Partial refund of $5.00
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List PaymentIntents</h2>
    <div class="method-signature">
        <pre><code>public function listPaymentIntent(array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$paymentIntents = $stripeGateway->listPaymentIntent([
    'limit' => 10,
    'customer' => 'cus_1234567890'
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Update PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function updatePaymentIntent(string $paymentIntentId, array $params): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$updated = $stripeGateway->updatePaymentIntent('pi_1234567890', [
    'metadata' => ['order_id' => '12345']
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Increment Authorization</h2>
    <div class="method-signature">
        <pre><code>public function incrementAuthorization(string $paymentIntentId, int $amount): \Stripe\PaymentIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$incremented = $stripeGateway->incrementAuthorization('pi_1234567890', 500);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Search PaymentIntents</h2>
    <div class="method-signature">
        <pre><code>public function searchPaymentIntent(array $params): \Stripe\SearchResult</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$results = $stripeGateway->searchPaymentIntent([
    'query' => 'status:"succeeded" AND metadata["order_id"]:"12345"'
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Validate PaymentIntent</h2>
    <div class="method-signature">
        <pre><code>public function isValidPaymentIntent(string $paymentIntentId): bool</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$isValid = $stripeGateway->isValidPaymentIntent('pi_1234567890');
if ($isValid) {
    echo "PaymentIntent is valid and succeeded";
}</code></pre>
    </div>
</section>

<script>
    document.getElementById('payment-intent-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('result-container');
        const resultOutput = document.getElementById('result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/payment-intent', {
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