import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface NavLinkProps {
    href: string;
    active?: boolean;
    children: ReactNode;
}

export default function NavLink({ href, active = false, children }: NavLinkProps) {
    return (
        <Link
            href={href}
            className={`px-3 py-2 rounded-md text-sm font-medium transition-colors ${
                active
                    ? 'bg-ml-primary-100 text-ml-primary-700'
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            }`}
        >
            {children}
        </Link>
    );
}
