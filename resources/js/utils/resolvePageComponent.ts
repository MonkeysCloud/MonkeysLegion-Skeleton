/**
 * Resolve a page component from a glob of eager-imported modules.
 *
 * @param pages Record of path → module (from import.meta.glob)
 * @param name The page path to resolve (e.g., './Pages/Dashboard.tsx')
 * @returns The resolved component
 */
export function resolvePageComponent(
    pages: Record<string, any>,
    name: string
): any {
    const page = pages[name];

    if (!page) {
        throw new Error(`Page "${name}" not found. Available: ${Object.keys(pages).join(', ')}`);
    }

    // Support both default exports and module.exports
    return page.default ?? page;
}
