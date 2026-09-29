import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/register', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50">
            <Head title="Register" />
            <div className="card w-full max-w-md">
                <h1 className="text-2xl font-bold text-center mb-6">Create Account</h1>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" className="input" value={data.name}
                            onChange={(e) => setData('name', e.target.value)} required />
                        {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" className="input" value={data.email}
                            onChange={(e) => setData('email', e.target.value)} required />
                        {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" className="input" value={data.password}
                            onChange={(e) => setData('password', e.target.value)} required />
                        {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" className="input" value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)} required />
                    </div>
                    <button type="submit" disabled={processing} className="btn-primary w-full">
                        {processing ? 'Creating...' : 'Register'}
                    </button>
                </form>
                <p className="text-center mt-4 text-sm text-gray-600">
                    Already have an account?{' '}
                    <Link href="/login" className="text-ml-primary-600 hover:underline">Sign In</Link>
                </p>
            </div>
        </div>
    );
}
