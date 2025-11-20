/**
 * MonkeysLegion Dev Server - Hot Reload Client
 *
 * This script polls the dev server for file changes and automatically
 * reloads the page when changes are detected.
 */
(function () {
    'use strict';

    // Configuration
    const POLL_INTERVAL = 1000; // Check every 1 second
    const ENDPOINT = '/_dev/reload.json';
    const DEBUG = true; // Set to false to disable console logs

    let lastVersion = null;
    let failCount = 0;
    const MAX_FAILS = 5;

    function log(...args) {
        if (DEBUG) {
            console.log('[DevServer Hot Reload]', ...args);
        }
    }

    async function checkReload() {
        try {
            const url = ENDPOINT + '?t=' + Date.now();
            const res = await fetch(url, {
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });

            if (!res.ok) {
                failCount++;
                if (failCount >= MAX_FAILS) {
                    log('Dev server endpoint not responding, stopping checks');
                    return false; // Stop polling
                }
                return true; // Continue polling
            }

            // Reset fail count on success
            failCount = 0;

            const data = await res.json();

            if (!data || typeof data.version === 'undefined') {
                log('Invalid response from dev server:', data);
                return true;
            }

            log('Polled version:', data.version, 'Last:', lastVersion);

            if (lastVersion === null) {
                // First check: just store current version
                lastVersion = data.version;
                log('Hot reload initialized, version:', lastVersion);
                return true;
            }

            if (data.version !== lastVersion && data.version > 0) {
                log('Change detected! Reloading page...');
                log('  Old version:', lastVersion);
                log('  New version:', data.version);

                // Add a small delay to ensure file changes are complete
                setTimeout(() => {
                    window.location.reload();
                }, 100);

                return false; // Stop polling (we're reloading)
            }

            return true; // Continue polling

        } catch (error) {
            failCount++;
            log('Error checking for reload:', error.message);

            if (failCount >= MAX_FAILS) {
                log('Too many failures, stopping hot reload checks');
                return false;
            }

            return true; // Continue polling despite error
        }
    }

    // Start polling
    log('Starting hot reload polling...');

    let intervalId = setInterval(async () => {
        const shouldContinue = await checkReload();
        if (!shouldContinue) {
            clearInterval(intervalId);
            log('âœ‹  Stopped polling');
        }
    }, POLL_INTERVAL);

    // Show dev mode indicator
    const devBadge = document.createElement('div');
    devBadge.id = 'dev-mode-badge';
    devBadge.innerHTML = 'DEV MODE';
    devBadge.style.cssText = `
        position: fixed;
        bottom: 10px;
        right: 80px;
        background: #000;
        color: #0f0;
        padding: 8px 12px;
        font-size: 11px;
        border-radius: 6px;
        z-index: 9999;
        font-family: monospace;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        cursor: help;
    `;
    devBadge.title = 'Development mode with hot reload enabled';

    // Add to DOM when ready
    if (document.body) {
        document.body.appendChild(devBadge);
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            document.body.appendChild(devBadge);
        });
    }

    log('Hot reload client initialized');
})();