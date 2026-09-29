import { ReactNode } from 'react';
import { Link, usePage } from '@inertiajs/react';
import NavLink from '@/Components/NavLink';

interface AuthenticatedLayoutProps {
    title?: string;
    children: ReactNode;
}

export default function AuthenticatedLayout({ title, children }: AuthenticatedLayoutProps) {
    const { auth } = usePage().props as { auth?: { user?: { name: string } } };

    return (
        <div className="min-h-screen bg-gray-50">
            <nav className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex items-center space-x-8">
                            <Link href="/dashboard" className="text-xl font-bold text-ml-primary-600">
                                MonKeysLegion
                            </Link>
                            <div className="hidden md:flex space-x-4">
                                <NavLink href="/dashboard" active={window.location.pathname === '/dashboard'}>
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>
                        <div className="flex items-center space-x-4">
                            {auth?.user ? (
                                <span className="text-sm text-gray-600">
                                    {auth.user.name}
                                </span>
                            ) : (
                                <Link href="/login" className="text-sm text-ml-primary-600 hover:underline">
                                    Login
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </nav>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {title && <h1 className="text-2xl font-bold mb-6">{title}</h1>}
                {children}
            </main>
        </div>
    );
}
