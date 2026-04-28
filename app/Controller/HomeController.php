<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;

/**
 * Welcome page — serves as both the landing screen and a template engine showcase.
 *
 * This controller demonstrates:
 * - Attribute-based routing (#[Route])
 * - Constructor injection (Renderer)
 * - Data binding to templates
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
        summary: 'Render welcome page',
        tags: ['Page'],
    )]
    public function index(): Response
    {
        $html = $this->renderer->render('home', [
            'title'       => 'Welcome — MonkeysLegion v2',
            'phpVersion'  => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            'mlVersion'   => 'v2.0',
            'environment' => $_ENV['APP_ENV'] ?? 'dev',

            // Quick-start navigation cards
            'cards' => [
                [
                    'title'       => 'Documentation',
                    'description' => 'Guides, API references, and tutorials to get started.',
                    'url'         => 'https://monkeyslegion.com/docs',
                    'external'    => true,
                    'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
                ],
                [
                    'title'       => 'Template Engine',
                    'description' => 'Layouts, components, slots, and directives.',
                    'url'         => 'https://monkeyslegion.com/docs/templates',
                    'external'    => true,
                    'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
                ],
                [
                    'title'       => 'Routing',
                    'description' => 'Attribute routes, middleware, and RESTful resources.',
                    'url'         => 'https://monkeyslegion.com/docs/routing',
                    'external'    => true,
                    'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
                ],
                [
                    'title'       => 'Configuration',
                    'description' => 'MLC config files, env variables, and typed access.',
                    'url'         => 'https://monkeyslegion.com/docs/configuration',
                    'external'    => true,
                    'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
                ],
            ],

            // Template engine feature showcase — clean strings, no workarounds
            'features' => [
                ['syntax' => '@extends(\'layout\')', 'label' => 'Layout inheritance'],
                ['syntax' => '@section / @yield',    'label' => 'Content sections'],
                ['syntax' => '{{ $var }}',            'label' => 'Escaped output'],
                ['syntax' => '{!! $html !!}',         'label' => 'Raw HTML output'],
                ['syntax' => '@if / @foreach',        'label' => 'Control directives'],
                ['syntax' => '<x-component>',         'label' => 'Components & slots'],
                ['syntax' => '@push / @stack',         'label' => 'Asset stacking'],
                ['syntax' => '@env(\'dev\')',          'label' => 'Environment checks'],
            ],
        ]);

        return Response::html($html);
    }
}