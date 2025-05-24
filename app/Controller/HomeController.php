<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;

/**
 * HomeController is responsible for rendering the home page.
 */
final class HomeController
{
    public function __construct(private Renderer $renderer) {}

    /**
     * Render the home page.
     *
     * @return Response
     */
    #[Route(
        methods: 'GET',
        path:    '/',
        name:    'home',
        summary: 'Render home page',
        tags:    ['Page']
    )]
    public function index(): Response
    {
        // 1) Render template
        $html = $this->renderer->render('home', [
            'title' => 'Home',
        ]);

        // 2) Build a Stream from the HTML
        $body = Stream::createFromString($html);

        // 3) Return the MonkeysLegion PSR-7 Response
        return new Response(
            $body,
            200,
            ['Content-Type' => 'text/html']
        );
    }
}