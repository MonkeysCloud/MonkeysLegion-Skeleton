<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;

final class HomeController
{
    #[Route('GET', '/')]
    public function index(): Response
    {
        return new Response(
            Stream::createFromString('<h2>Hello from MonkeysLegion!</h2>'),
            200,
            ['Content-Type' => 'text/html']
        );
    }
}