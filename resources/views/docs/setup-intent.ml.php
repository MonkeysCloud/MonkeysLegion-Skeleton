@extends('layouts.docs')

@section('header')
<h1>SetupIntent Demo</h1>
<p>Test SetupIntent creation - saves payment methods for future use</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Create SetupIntent Demo</h2>
    <p><strong>Note:</strong> SetupIntents save payment methods without charging. Used for storing cards for future payments.</p>

    <div class="example-form">
        <h3>Interactive Test</h3>
        <form id="setup-intent-form">
            <div class="form-group">
                <label class="form-label" for="usage">Usage</label>
                <select id="usage" name="usage" class="form-input">
                    <option value="off_session">Off Session</option>
                    <option value="on_session">On Session</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create SetupIntent</button>
        </form>
    </div>
    <div id="setup-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="setup-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve SetupIntent Demo</h2>
    <p><strong>Note:</strong> Get details of an existing setup intent by ID.</p>

    <div class="example-form">
        <h3>Interactive Test - Retrieve SetupIntent</h3>
        <form id="retrieve-setup-form">
            <div class="form-group">
                <label class="form-label" for="setup_intent_id">SetupIntent ID</label>
                <input type="text" id="setup_intent_id" name="setup_intent_id" class="form-input" placeholder="seti_...">
            </div>
            <button type="submit" class="btn btn-info">Retrieve SetupIntent</button>
        </form>
    </div>

    <div id="retrieve-setup-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="retrieve-setup-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Confirm SetupIntent Demo</h2>
    <p><strong>Note:</strong> Confirm a setup intent with a payment method.</p>

    <div class="example-form">
        <h3>Interactive Test - Confirm SetupIntent</h3>
        <form id="confirm-setup-form">
            <div class="form-group">
                <label class="form-label" for="confirm_setup_id">SetupIntent ID</label>
                <input type="text" id="confirm_setup_id" name="setup_intent_id" class="form-input" placeholder="seti_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="setup_payment_method">Payment Method ID</label>
                <input type="text" id="setup_payment_method" name="payment_method" class="form-input" placeholder="pm_card_visa">
            </div>
            <button type="submit" class="btn btn-success">Confirm SetupIntent</button>
        </form>
    </div>

    <div id="confirm-setup-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="confirm-setup-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Cancel SetupIntent Demo</h2>
    <p><strong>Note:</strong> Cancel a setup intent before confirmation.</p>

    <div class="example-form">
        <h3>Interactive Test - Cancel SetupIntent</h3>
        <form id="cancel-setup-form">
            <div class="form-group">
                <label class="form-label" for="cancel_setup_id">SetupIntent ID</label>
                <input type="text" id="cancel_setup_id" name="setup_intent_id" class="form-input" placeholder="seti_...">
            </div>
            <button type="submit" class="btn btn-warning">Cancel SetupIntent</button>
        </form>
    </div>

    <div id="cancel-setup-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="cancel-setup-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List SetupIntents Demo</h2>
    <p><strong>Note:</strong> List recent setup intents with optional filters.</p>

    <div class="example-form">
        <h3>Interactive Test - List SetupIntents</h3>
        <form id="list-setup-form">
            <div class="form-group">
                <label class="form-label" for="setup_limit">Limit (max results)</label>
                <input type="number" id="setup_limit" name="limit" class="form-input" value="10" min="1" max="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="setup_customer">Customer ID (optional)</label>
                <input type="text" id="setup_customer" name="customer_id" class="form-input" placeholder="cus_...">
            </div>
            <button type="submit" class="btn btn-info">List SetupIntents</button>
        </form>
    </div>

    <div id="list-setup-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="list-setup-result-output"><code></code></pre>
    </div>
</section>

<script>
    document.getElementById('setup-intent-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('setup-result-container');
        const resultOutput = document.getElementById('setup-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/setup-intent', {
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

    // Retrieve SetupIntent Handler
    document.getElementById('retrieve-setup-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('retrieve-setup-result-container');
        const resultOutput = document.getElementById('retrieve-setup-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/setup-intent/retrieve', {
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

    // Confirm SetupIntent Handler
    document.getElementById('confirm-setup-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('confirm-setup-result-container');
        const resultOutput = document.getElementById('confirm-setup-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/setup-intent/confirm', {
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

    // Cancel SetupIntent Handler
    document.getElementById('cancel-setup-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('cancel-setup-result-container');
        const resultOutput = document.getElementById('cancel-setup-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/setup-intent/cancel', {
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

    // List SetupIntents Handler
    document.getElementById('list-setup-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('list-setup-result-container');
        const resultOutput = document.getElementById('list-setup-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/setup-intent/list', {
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