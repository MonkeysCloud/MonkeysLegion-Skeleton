<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;

/**
 * Renders public web pages.
 */
final class HomeController
{
    public function __construct(
        private readonly Renderer $renderer,
    ) {}

    #[Route(
        methods: 'GET',
        path: '/',
        name: 'home',
        summary: 'Render home page',
        tags: ['Page'],
    )]
    public function index(): Response
    {
        $html = $this->renderer->render('home', [
            'title' => 'Home',
        ]);

        return Response::html($html);
    }
}