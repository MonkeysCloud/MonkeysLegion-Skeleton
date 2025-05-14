<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;
use Psr\Http\Message\ServerRequestInterface;

final class HomeController
{
    #[Route('GET', '/')]
    public function index(
        ServerRequestInterface $request,
        Renderer               $view
    ): Response {
        $html = $view->render('home', [
            'title' => 'Home',
        ]);

        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }
}