@extends('layouts.docs')

@section('header')
<h1>Product Documentation</h1>
<p>Learn how to create and manage Stripe Products with the MonkeysLegion Stripe Package</p>
@endsection

@section('content')
<section class="docs-section">
    <h2>Overview</h2>
    <p>Products represent the goods or services you offer to your customers. Products can be used with Prices to define what and how much your customers pay.</p>
</section>

<section class="docs-section">
    <h2>Create Product</h2>
    <div class="method-signature">
        <pre><code>public function createProduct(array $params): \Stripe\Product</code></pre>
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
                <td><code>$params['name']</code></td>
                <td>string</td>
                <td>Yes</td>
                <td>The product's name</td>
            </tr>
            <tr>
                <td><code>$params['description']</code></td>
                <td>string</td>
                <td>No</td>
                <td>The product's description</td>
            </tr>
            <tr>
                <td><code>$params['active']</code></td>
                <td>boolean</td>
                <td>No</td>
                <td>Whether the product is active (default: true)</td>
            </tr>
            <tr>
                <td><code>$params['images']</code></td>
                <td>array</td>
                <td>No</td>
                <td>Array of image URLs for the product</td>
            </tr>
            <tr>
                <td><code>$params['metadata']</code></td>
                <td>array</td>
                <td>No</td>
                <td>Set of key-value pairs for custom data</td>
            </tr>
        </tbody>
    </table>

    <div class="code-example">
        <h3>Example</h3>
        <pre><code>$product = $productService->createProduct([
    'name' => 'Premium T-shirt',
    'description' => 'Soft cotton t-shirt',
    'active' => true,
    'images' => ['https://example.com/t-shirt.jpg'],
    'metadata' => ['category' => 'clothing']
]);</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test</h3>
        <form id="product-form">
            <div class="form-group">
                <label class="form-label" for="name">Product Name</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Premium T-shirt">
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description (optional)</label>
                <textarea id="description" name="description" class="form-input" placeholder="Soft cotton t-shirt"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="active">Active</label>
                <select id="active" name="active" class="form-input">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="images">Images (comma-separated URLs, optional)</label>
                <input type="text" id="images" name="images" class="form-input" placeholder="https://example.com/image1.jpg, https://example.com/image2.jpg">
            </div>
            <div class="form-group">
                <label class="form-label" for="metadata">Metadata (JSON, optional)</label>
                <textarea id="metadata" name="metadata" class="form-input" placeholder='{"category": "clothing"}'></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Product</button>
        </form>
    </div>

    <div id="product-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="product-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Retrieve Product</h2>
    <div class="method-signature">
        <pre><code>public function retrieveProduct(string $productId, array $params = []): \Stripe\Product</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$product = $productService->retrieveProduct('prod_1234567890');</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Update Product</h2>
    <div class="method-signature">
        <pre><code>public function updateProduct(string $productId, array $params): \Stripe\Product</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$updated = $productService->updateProduct('prod_1234567890', [
    'name' => 'Updated Product Name',
    'metadata' => ['category' => 'new-category']
]);</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test - Update Product</h3>
        <form id="update-product-form">
            <div class="form-group">
                <label class="form-label" for="product_id">Product ID</label>
                <input type="text" id="product_id" name="product_id" class="form-input" placeholder="prod_...">
            </div>
            <div class="form-group">
                <label class="form-label" for="update_name">New Name (optional)</label>
                <input type="text" id="update_name" name="name" class="form-input" placeholder="Updated Product Name">
            </div>
            <div class="form-group">
                <label class="form-label" for="update_description">New Description (optional)</label>
                <textarea id="update_description" name="description" class="form-input" placeholder="Updated description"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="update_active">Active</label>
                <select id="update_active" name="active" class="form-input">
                    <option value="">Don't change</option>
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="update_metadata">New Metadata (JSON, optional)</label>
                <textarea id="update_metadata" name="metadata" class="form-input" placeholder='{"category": "new-category"}'></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>

    <div id="update-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="update-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Delete Product</h2>
    <div class="method-signature">
        <pre><code>public function deleteProduct(string $productId, array $options = []): \Stripe\Product</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$deleted = $productService->deleteProduct('prod_1234567890');</code></pre>
    </div>

    <div class="example-form">
        <h3>Interactive Test - Delete Product</h3>
        <form id="delete-product-form">
            <div class="form-group">
                <label class="form-label" for="delete_product_id">Product ID</label>
                <input type="text" id="delete_product_id" name="product_id" class="form-input" placeholder="prod_...">
            </div>
            <button type="submit" class="btn btn-danger">Delete Product</button>
        </form>
    </div>

    <div id="delete-result-container" class="result-container" style="display: none;">
        <h3>Response</h3>
        <pre id="delete-result-output"><code></code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>List Products</h2>
    <div class="method-signature">
        <pre><code>public function listProducts(array $params = []): \Stripe\Collection</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$products = $productService->listProducts([
    'active' => true,
    'limit' => 10
]);</code></pre>
    </div>
</section>

<section class="docs-section">
    <h2>Search Products</h2>
    <div class="method-signature">
        <pre><code>public function searchProducts(string $query, array $params = []): \Stripe\SearchResult</code></pre>
    </div>

    <div class="code-example">
        <pre><code>$results = $productService->searchProducts(
    'active:"true" AND metadata["category"]:"clothing"'
);</code></pre>
    </div>
</section>

<script>
    document.getElementById('product-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('product-result-container');
        const resultOutput = document.getElementById('product-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/product', {
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

    document.getElementById('update-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('update-result-container');
        const resultOutput = document.getElementById('update-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/product/update', {
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

    document.getElementById('delete-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            return;
        }

        const formData = new FormData(this);
        const resultContainer = document.getElementById('delete-result-container');
        const resultOutput = document.getElementById('delete-result-output').querySelector('code');

        try {
            const response = await fetch('/stripe/product/delete', {
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