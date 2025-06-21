@extends('layouts.docs')

@section('header')
<h1>PaymentIntent Demo</h1>
<p>Test Stripe PaymentIntent creation - handles payment processing</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Create PaymentIntent Demo</h2>
    <p><strong>Note:</strong> Creates payment intent for card payments. Amount in cents (1000 = $10.00)</p>

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
    <h2>Retrieve PaymentIntent Demo</h2>
    <p><strong>Note:</strong> Get details of an existing payment intent by ID.</p>

    <div class="example-form">
        <h3>Interactive Test - Retrieve Payment</h3>
        <form id="retrieve-payment-form">
            <div class="form-group">
                <label class="form-label" for="payment_intent_id">PaymentIntent ID</label>
                <input type="text" id="payment_intent_id" name="payment_intent_id" class="form-input" placeholder="pi_...">
            </div>
            <button type="submit" class="btn btn-info">Retrieve PaymentIntent</button>
        </form>
    </div>

    <div id="retrieve-payment-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="retrieve-payment-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Confirm PaymentIntent Demo</h2>
    <p><strong>Note:</strong> Confirm a payment intent with a payment method.</p>

    <div class="example-form">
        <h3>Interactive Test - Confirm Payment</h3>
        <form id="confirm-payment-form">
            <div class="form-group">
                <label class="form-label" for="confirm_payment_id">PaymentIntent ID</label>
                <input type="text" id="confirm_payment_id" name="payment_intent_id" class="form-input" placeholder="pi_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="payment_method">Payment Method ID</label>
                <input type="text" id="payment_method" name="payment_method" class="form-input" placeholder="pm_card_visa">
            </div>
            <button type="submit" class="btn btn-success">Confirm Payment</button>
        </form>
    </div>

    <div id="confirm-payment-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="confirm-payment-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Cancel PaymentIntent Demo</h2>
    <p><strong>Note:</strong> Cancel a payment intent before confirmation.</p>

    <div class="example-form">
        <h3>Interactive Test - Cancel Payment</h3>
        <form id="cancel-payment-form">
            <div class="form-group">
                <label class="form-label" for="cancel_payment_id">PaymentIntent ID</label>
                <input type="text" id="cancel_payment_id" name="payment_intent_id" class="form-input" placeholder="pi_...">
            </div>
            <button type="submit" class="btn btn-warning">Cancel Payment</button>
        </form>
    </div>

    <div id="cancel-payment-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="cancel-payment-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List PaymentIntents Demo</h2>
    <p><strong>Note:</strong> List recent payment intents with optional filters.</p>

    <div class="example-form">
        <h3>Interactive Test - List Payments</h3>
        <form id="list-payments-form">
            <div class="form-group">
                <label class="form-label" for="payments_limit">Limit (max results)</label>
                <input type="number" id="payments_limit" name="limit" class="form-input" value="10" min="1" max="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="customer_id">Customer ID (optional)</label>
                <input type="text" id="customer_id" name="customer_id" class="form-input" placeholder="cus_...">
            </div>
            <button type="submit" class="btn btn-info">List PaymentIntents</button>
        </form>
    </div>

    <div id="list-payments-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="list-payments-result-output"><code></code></pre>
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

    // Retrieve PaymentIntent Handler
    document.getElementById('retrieve-payment-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('retrieve-payment-result-container');
        const resultOutput = document.getElementById('retrieve-payment-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/payment-intent/retrieve', {
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

    // Confirm PaymentIntent Handler
    document.getElementById('confirm-payment-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('confirm-payment-result-container');
        const resultOutput = document.getElementById('confirm-payment-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/payment-intent/confirm', {
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

    // Cancel PaymentIntent Handler
    document.getElementById('cancel-payment-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('cancel-payment-result-container');
        const resultOutput = document.getElementById('cancel-payment-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/payment-intent/cancel', {
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

    // List PaymentIntents Handler
    document.getElementById('list-payments-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('list-payments-result-container');
        const resultOutput = document.getElementById('list-payments-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/payment-intent/list', {
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