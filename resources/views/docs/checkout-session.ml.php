@extends('layouts.docs')

@section('header')
<h1>Checkout Session Demo</h1>
<p>Test Stripe Checkout sessions for hosted payment pages</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Create Checkout Session Demo</h2>
    <p><strong>Note:</strong> Creates hosted checkout page. Redirects users to Stripe-hosted payment form.</p>

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
    <p><strong>Note:</strong> Creates session and returns URL directly for immediate redirection.</p>

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
            </div> <button type="submit" class="btn btn-secondary">Go to Checkout (Redirect)</button>
        </form>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve Checkout Session Demo</h2>
    <p><strong>Note:</strong> Get details of an existing checkout session by ID.</p>

    <div class="example-form">
        <h3>Interactive Test - Retrieve Session</h3>
        <form id="retrieve-session-form">
            <div class="form-group">
                <label class="form-label" for="session_id">Session ID</label>
                <input type="text" id="session_id" name="session_id" class="form-input" placeholder="cs_...">
            </div>
            <button type="submit" class="btn btn-info">Retrieve Session</button>
        </form>
    </div>

    <div id="retrieve-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="retrieve-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Checkout Sessions Demo</h2>
    <p><strong>Note:</strong> List recent checkout sessions with optional filters.</p>

    <div class="example-form">
        <h3>Interactive Test - List Sessions</h3>
        <form id="list-sessions-form">
            <div class="form-group">
                <label class="form-label" for="limit">Limit (max results)</label>
                <input type="number" id="limit" name="limit" class="form-input" value="10" min="1" max="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="created_gte">Created After (optional)</label>
                <input type="datetime-local" id="created_gte" name="created_gte" class="form-input">
            </div>
            <button type="submit" class="btn btn-info">List Sessions</button>
        </form>
    </div>

    <div id="list-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="list-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Expire Checkout Session Demo</h2>
    <p><strong>Note:</strong> Manually expire an active checkout session to prevent further use.</p>

    <div class="example-form">
        <h3>Interactive Test - Expire Session</h3>
        <form id="expire-session-form">
            <div class="form-group">
                <label class="form-label" for="expire_session_id">Session ID</label>
                <input type="text" id="expire_session_id" name="session_id" class="form-input" placeholder="cs_...">
            </div>
            <button type="submit" class="btn btn-warning">Expire Session</button>
        </form>
    </div>

    <div id="expire-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="expire-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Line Items Demo</h2>
    <p><strong>Note:</strong> Get line items from a completed checkout session.</p>

    <div class="example-form">
        <h3>Interactive Test - List Line Items</h3>
        <form id="line-items-form">
            <div class="form-group">
                <label class="form-label" for="line_items_session_id">Session ID</label>
                <input type="text" id="line_items_session_id" name="session_id" class="form-input" placeholder="cs_...">
            </div>
            <button type="submit" class="btn btn-info">Get Line Items</button>
        </form>
    </div>

    <div id="line-items-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="line-items-result-output"><code></code></pre>
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

    // Retrieve Session Handler
    document.getElementById('retrieve-session-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('retrieve-result-container');
        const resultOutput = document.getElementById('retrieve-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/checkout-session/retrieve', {
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

    // List Sessions Handler
    document.getElementById('list-sessions-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('list-result-container');
        const resultOutput = document.getElementById('list-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/checkout-session/list', {
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

    // Expire Session Handler
    document.getElementById('expire-session-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('expire-result-container');
        const resultOutput = document.getElementById('expire-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/checkout-session/expire', {
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

    // Line Items Handler
    document.getElementById('line-items-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('line-items-result-container');
        const resultOutput = document.getElementById('line-items-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/checkout-session/line-items', {
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