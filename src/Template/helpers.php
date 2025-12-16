<?php
declare(strict_types=1);

/**
 * Asset versioning helper for cache-busting static files.
 *
 * Usage in templates:
 *   <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
 *   <script src="<?= asset('js/app.js') ?>"></script>
 *
 * Looks for a manifest.json in public/assets:
 *   { "css/app.css": "css/app.abcdef.css", ... }
 * Fallback: appends ?v=filemtime if manifest entry is missing.
 */

use MonkeysLegion\I18n\Translator;
use Psr\Http\Message\ServerRequestInterface;

if (! function_exists('asset')) {
    function asset(string $path): string
    {
        static $manifest = null;

        // Load manifest once
        if ($manifest === null) {
            $manifestPath = base_path('public/assets/manifest.json');
            if (is_file($manifestPath)) {
                $content = file_get_contents($manifestPath);
                $manifest = $content !== false ? (json_decode($content, true) ?: []) : [];
            } else {
                $manifest = [];
            }
        }

        // Determine the actual file name
        if (isset($manifest[$path])) {
            $file = $manifest[$path];
        } else {
            $file = ltrim($path, '/');
        }

        $url = '/assets/' . $file;

        // If no manifest entry, append file modification timestamp
        if (!isset($manifest[$path])) {
            $physical = base_path('public/assets/' . $file);
            if (is_file($physical)) {
                $url .= '?v=' . filemtime($physical);
            }
        }

        return $url;
    }
}

if (!function_exists('trans')) {
    /**
     * @param array<string, string> $replace
     */
    function trans(string $key, array $replace = []): string {
        /** @var Translator $t */
        $t = ML_CONTAINER->get(Translator::class);
        return $t->trans($key, $replace);
    }
}

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '" />';
    }
}

if (!function_exists('auth_user_id')) {
    function auth_user_id(): ?int
    {
        /** @var ServerRequestInterface $req */
        $req = ML_CONTAINER->get(ServerRequestInterface::class);
        return $req->getAttribute('userId');
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return auth_user_id() !== null;
    }
}
