<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;

/**
 * Static web pages.
 */
final class PageController
{
    public function __construct(
        private readonly Renderer $renderer,
    ) {}

    #[Route(
        methods: 'GET',
        path: '/about',
        name: 'about',
        summary: 'About page',
        tags: ['Page'],
    )]
    public function about(): Response
    {
        $html = $this->renderer->render('home', [
            'title' => 'About MonkeysLegion',
        ]);

        return Response::html($html);
    }
}
