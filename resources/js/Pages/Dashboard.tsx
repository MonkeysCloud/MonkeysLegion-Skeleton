import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

interface DashboardProps {
    stats: {
        users: number;
        posts: number;
        comments: number;
    };
}

export default function Dashboard({ stats }: DashboardProps) {
    const { flash } = usePage().props as { flash?: { success?: string; error?: string } };

    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />

            {flash?.success && (
                <div className="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {flash.success}
                </div>
            )}
            {flash?.error && (
                <div className="mb-4 rounded-lg bg-red-50 p-4 text-red-800">
                    {flash.error}
                </div>
            )}

            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div className="card">
                    <h3 className="text-lg font-semibold text-gray-700">Users</h3>
                    <p className="text-3xl font-bold text-ml-primary-600">{stats.users}</p>
                </div>
                <div className="card">
                    <h3 className="text-lg font-semibold text-gray-700">Posts</h3>
                    <p className="text-3xl font-bold text-ml-primary-600">{stats.posts}</p>
                </div>
                <div className="card">
                    <h3 className="text-lg font-semibold text-gray-700">Comments</h3>
                    <p className="text-3xl font-bold text-ml-primary-600">{stats.comments}</p>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
