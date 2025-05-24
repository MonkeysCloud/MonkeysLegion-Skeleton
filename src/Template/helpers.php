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

if (! function_exists('asset')) {
    function asset(string $path): string
    {
        static $manifest = null;

        // Load manifest once
        if ($manifest === null) {
            $manifestPath = base_path('public/assets/manifest.json');
            if (is_file($manifestPath)) {
                $content = file_get_contents($manifestPath);
                $manifest = json_decode($content, true) ?: [];
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
    function trans(string $key, array $replace = []): string {
        /** @var Translator $t */
        $t = ML_CONTAINER->get(Translator::class);
        return $t->trans($key, $replace);
    }
}
