@extends('layouts.docs')

@section('header')
<h1>SetupIntent Documentation</h1>
<p>Learn how to create and manage SetupIntents for saving payment methods</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Overview</h2>
    <p>SetupIntents are used to set up payment methods for future payments. They're ideal for saving cards or other payment methods without charging immediately.</p>
</section>

<section class="docs-section">
    <h2>Create SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function createSetupIntent(array $params): \Stripe\SetupIntent</code></pre>
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
                <td><code>$params['usage']</code></td>
                <td>string</td>
                <td>No</td>
                <td>How the payment method will be used (default: 'off_session')</td>
            </tr>
            <tr>
                <td><code>$params['payment_method_types']</code></td>
                <td>array</td>
                <td>No</td>
                <td>Payment method types to allow (default: ['card'])</td>
            </tr>
        </tbody>
    </table>

    <div class="code-example">
        <h3>Example</h3>
        <pre><code>$setupIntent = $setupIntentService->createSetupIntent([
    'usage' => 'off_session',
    'payment_method_types' => ['card'] // Optional, defaults to ['card']
]);</code></pre>
    </div>

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
    <h2>Retrieve SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function retrieveSetupIntent(string $setupIntentId): \Stripe\SetupIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$setupIntent = $setupIntentService->retrieveSetupIntent('seti_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Confirm SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function confirmSetupIntent(string $setupIntentId, array $params): \Stripe\SetupIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$confirmed = $setupIntentService->confirmSetupIntent('seti_1234567890', [
    'payment_method' => 'pm_card_visa'
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Cancel SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function cancelSetupIntent(string $setupIntentId): \Stripe\SetupIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$cancelled = $setupIntentService->cancelSetupIntent('seti_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List SetupIntents</h2>
    <div class="method-signature">
        <pre><code>public function listSetupIntents(array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$setupIntents = $setupIntentService->listSetupIntents([
    'customer' => 'cus_1234567890',
    'limit' => 10
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Update SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function updateSetupIntent(string $setupIntentId, array $params): \Stripe\SetupIntent</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$updated = $setupIntentService->updateSetupIntent('seti_1234567890', [
    'metadata' => ['order_id' => '12345']
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Validate SetupIntent</h2>
    <div class="method-signature">
        <pre><code>public function isValidSetupIntent(string $setupIntentId): bool</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$isValid = $setupIntentService->isValidSetupIntent('seti_1234567890');
if ($isValid) {
    echo "SetupIntent is valid and succeeded";
}</code></pre>
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
</script>
@endsection