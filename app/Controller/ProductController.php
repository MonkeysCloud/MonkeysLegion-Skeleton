<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Stripe\Client\Product;
use MonkeysLegion\Stripe\Service\ServiceContainer;
use MonkeysLegion\Template\Renderer;

/**
 * ProductController is responsible for handling Stripe product-related actions.
 */
final class ProductController
{
    private $ProductService;

    public function __construct(private Renderer $renderer)
    {
        $this->ProductService = ML_CONTAINER->get(Product::class);
    }

    /**
     * Create a Stripe Product.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/product',
        name: 'stripe.product',
        summary: 'Create Stripe Product',
        tags: ['Product']
    )]
    public function createProduct(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $name = $_POST['name'] ?? '';

            if (empty($name)) {
                throw new \InvalidArgumentException('Product name is required');
            }

            // Build the parameters array for product creation
            $params = [
                'name' => $name,
            ];

            // Add description if provided
            if (!empty($_POST['description'])) {
                $params['description'] = $_POST['description'];
            }

            // Add active status if provided
            if (isset($_POST['active'])) {
                $params['active'] = $_POST['active'] === 'true';
            }

            // Add images if provided
            if (!empty($_POST['images'])) {
                $images = explode(',', $_POST['images']);
                $params['images'] = array_map('trim', $images);
            }

            // Add metadata if provided
            if (!empty($_POST['metadata'])) {
                $params['metadata'] = json_decode($_POST['metadata'], true);
            }

            $product = $this->ProductService->createProduct($params);

            $responseData = [
                'success' => true,
                'product_id' => $product->id,
                'name' => $product->name,
                'active' => $product->active,
                'description' => $product->description,
                'images' => $product->images,
                'metadata' => $product->metadata
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Update a Stripe Product.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/product/update',
        name: 'stripe.product.update',
        summary: 'Update Stripe Product',
        tags: ['Product']
    )]
    public function updateProduct(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $productId = $_POST['product_id'] ?? '';

            if (empty($productId)) {
                throw new \InvalidArgumentException('Product ID is required');
            }

            // Build the parameters array for product update
            $params = [];

            // Add name if provided
            if (!empty($_POST['name'])) {
                $params['name'] = $_POST['name'];
            }

            // Add description if provided
            if (isset($_POST['description'])) {
                $params['description'] = $_POST['description'];
            }

            // Add active status if provided
            if (isset($_POST['active'])) {
                $params['active'] = $_POST['active'] === 'true';
            }

            // Add metadata if provided
            if (!empty($_POST['metadata'])) {
                $params['metadata'] = json_decode($_POST['metadata'], true);
            }

            $product = $this->ProductService->updateProduct($productId, $params);

            $responseData = [
                'success' => true,
                'product_id' => $product->id,
                'name' => $product->name,
                'active' => $product->active,
                'description' => $product->description,
                'images' => $product->images,
                'metadata' => $product->metadata
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Delete a Stripe Product.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/product/delete',
        name: 'stripe.product.delete',
        summary: 'Delete Stripe Product',
        tags: ['Product']
    )]
    public function deleteProduct(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $productId = $_POST['product_id'] ?? '';

            if (empty($productId)) {
                throw new \InvalidArgumentException('Product ID is required');
            }

            $product = $this->ProductService->deleteProduct($productId);

            $responseData = [
                'success' => true,
                'product_id' => $product->id,
                'deleted' => $product->deleted
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * Retrieve a Stripe Product.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/product/retrieve',
        name: 'stripe.product.retrieve',
        summary: 'Retrieve Stripe Product',
        tags: ['Product']
    )]
    public function retrieveProduct(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $productId = $_POST['product_id'] ?? '';

            if (empty($productId)) {
                throw new \InvalidArgumentException('Product ID is required');
            }

            $product = $this->ProductService->retrieveProduct($productId);

            $responseData = [
                'success' => true,
                'product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'active' => $product->active,
                'images' => $product->images,
                'metadata' => $product->metadata,
                'created' => date('Y-m-d H:i:s', $product->created),
                'updated' => date('Y-m-d H:i:s', $product->updated)
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }

    /**
     * List Stripe Products.
     */
    #[Route(
        methods: 'POST',
        path: '/stripe/product/list',
        name: 'stripe.product.list',
        summary: 'List Stripe Products',
        tags: ['Product']
    )]
    public function listProducts(): Response
    {
        $headers = ['Content-Type' => 'application/json'];

        try {
            $params = [];

            if (!empty($_POST['limit']) && is_numeric($_POST['limit'])) {
                $params['limit'] = (int)$_POST['limit'];
            }

            if (isset($_POST['active']) && $_POST['active'] !== '') {
                $params['active'] = $_POST['active'] === 'true';
            }

            $products = $this->ProductService->listProducts($params);

            $responseData = [
                'success' => true,
                'products' => array_map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'active' => $product->active,
                        'images' => $product->images,
                        'created' => date('Y-m-d H:i:s', $product->created),
                        'updated' => date('Y-m-d H:i:s', $product->updated)
                    ];
                }, $products->data),
                'has_more' => $products->has_more
            ];

            return new Response(
                Stream::createFromString(json_encode($responseData)),
                200,
                $headers
            );
        } catch (\Exception $e) {
            $errorData = [
                'success' => false,
                'error' => $e->getMessage()
            ];

            return new Response(
                Stream::createFromString(json_encode($errorData)),
                400,
                $headers
            );
        }
    }
}
