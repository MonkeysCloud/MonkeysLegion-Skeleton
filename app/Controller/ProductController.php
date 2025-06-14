<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
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
        $c = ServiceContainer::getInstance();
        $this->ProductService = $c->get('ProductService');
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
}
