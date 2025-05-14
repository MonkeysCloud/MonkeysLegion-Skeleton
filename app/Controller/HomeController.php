<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;

final class HomeController
{
    #[Route('GET', '/')]
    public function index(): Response
    {
        // Grab the view renderer from the global container
        /** @var Renderer $view */
        $view = ML_CONTAINER->get(Renderer::class);

        // Render the 'home' template, passing the title
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