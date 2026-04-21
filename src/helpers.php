<?php
declare(strict_types=1);

/**
 * MonkeysLegion v2 — Global Helper Functions.
 *
 * Loaded via composer autoload.files for use in templates and app code.
 */

use MonkeysLegion\I18n\Translator;

use Psr\Http\Message\ServerRequestInterface;

// ── Path Helpers ──────────────────────────────────────────────

if (!function_exists('base_path')) {
    /**
     * Get the application base path.
     */
    function base_path(string $path = ''): string
    {
        $base = defined('ML_BASE_PATH') ? ML_BASE_PATH : getcwd();

        return $path !== '' ? rtrim($base, '/') . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the app/ directory path.
     */
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the config/ directory path.
     */
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage/ directory path.
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

// ── Asset Helpers ──────────────────────────────────────────────

if (!function_exists('asset')) {
    /**
     * Generate a versioned asset URL.
     *
     * Reads public/assets/manifest.json for cache-busted filenames.
     * Falls back to appending ?v=filemtime.
     */
    function asset(string $path): string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifestPath = base_path('public/assets/manifest.json');
            if (is_file($manifestPath)) {
                $content = file_get_contents($manifestPath);
                $manifest = $content !== false ? (json_decode($content, true) ?: []) : [];
            } else {
                $manifest = [];
            }
        }

        $file = $manifest[$path] ?? ltrim($path, '/');
        $url = '/assets/' . $file;

        if (!isset($manifest[$path])) {
            $physical = base_path('public/assets/' . $file);
            if (is_file($physical)) {
                $url .= '?v=' . filemtime($physical);
            }
        }

        return $url;
    }
}

// ── Translation Helpers ────────────────────────────────────────

if (!function_exists('trans')) {
    /**
     * @param array<string, string> $replace
     */
    function trans(string $key, array $replace = []): string
    {
        /** @var Translator $t */
        $t = \MonkeysLegion\DI\Container::instance()->get(Translator::class);

        return $t->trans($key, $replace);
    }
}

// ── CSRF Helpers ───────────────────────────────────────────────

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        /** @var \MonkeysLegion\Session\Contracts\SessionInterface|null $session */
        $session = \MonkeysLegion\DI\Container::instance()->get(SessionManager::class);

        return $session !== null ? $session->token() : '';
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '" />';
    }
}

// ── Auth Helpers ───────────────────────────────────────────────

if (!function_exists('auth_user_id')) {
    function auth_user_id(): ?int
    {
        /** @var ServerRequestInterface $req */
        $req = \MonkeysLegion\DI\Container::instance()->get(ServerRequestInterface::class);

        return $req->getAttribute('userId');
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return auth_user_id() !== null;
    }
}
