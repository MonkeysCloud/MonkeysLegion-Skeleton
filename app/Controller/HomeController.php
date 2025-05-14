<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;     // ← inject this

final class HomeController
{
    #[Route('GET', '/')]
    public function index(Renderer $view): Response
    {
        // Render the `home.ml.php` template, passing any variables:
        $html = $view->render('home', [
            'title' => 'Home'
        ]);

        return new Response(
            Stream::createFromString($html), // body first
            200,
            ['Content-Type' => 'text/html']
        );
    }
}