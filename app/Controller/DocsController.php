<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;

final class DocsController
{
    public function __construct(private Renderer $renderer) {}

    #[Route(
        methods: 'GET',
        path: '/docs',
        name: 'docs.index',
        summary: 'Documentation index page',
        tags: ['Documentation']
    )]
    public function index(): Response
    {
        return $this->redirectTo('/docs/payment-intent');
    }

    #[Route(
        methods: 'GET',
        path: '/docs/payment-intent',
        name: 'docs.payment-intent',
        summary: 'PaymentIntent documentation',
        tags: ['Documentation']
    )]
    public function paymentIntent(): Response
    {
        $html = $this->renderer->render('docs/payment-intent', [
            'title' => 'PaymentIntent Documentation',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    #[Route(
        methods: 'GET',
        path: '/docs/setup-intent',
        name: 'docs.setup-intent',
        summary: 'SetupIntent documentation',
        tags: ['Documentation']
    )]
    public function setupIntent(): Response
    {
        $html = $this->renderer->render('docs/setup-intent', [
            'title' => 'SetupIntent Documentation',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    #[Route(
        methods: 'GET',
        path: '/docs/checkout-session',
        name: 'docs.checkout-session',
        summary: 'Checkout Session documentation',
        tags: ['Documentation']
    )]
    public function checkoutSession(): Response
    {
        $html = $this->renderer->render('docs/checkout-session', [
            'title' => 'Checkout Session Documentation',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    #[Route(
        methods: 'GET',
        path: '/docs/subscription',
        name: 'docs.subscription',
        summary: 'Subscription documentation',
        tags: ['Documentation']
    )]
    public function subscription(): Response
    {
        $html = $this->renderer->render('docs/subscription', [
            'title' => 'Subscription Documentation',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    #[Route(
        methods: 'GET',
        path: '/docs/product',
        name: 'docs.product',
        summary: 'Product documentation',
        tags: ['Documentation']
    )]
    public function product(): Response
    {
        $html = $this->renderer->render('docs/product', [
            'title' => 'Product Documentation',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    private function redirectTo(string $url): Response
    {
        return new Response(
            Stream::createFromString(''),
            302,
            ['Location' => $url]
        );
    }
}
