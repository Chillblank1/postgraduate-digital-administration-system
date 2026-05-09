import { useForm, usePage } from '@inertiajs/react';

export default function Login() {
    const { appName } = usePage().props;
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    return (
        <div className="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6">
            <p className="mb-1 text-center text-sm font-medium text-slate-600">{appName}</p>
            <h1 className="mb-6 text-center text-2xl font-semibold">Sign in</h1>

            <form
                className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                onSubmit={(e) => {
                    e.preventDefault();
                    form.post('/login');
                }}
            >
                <div>
                    <label className="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        type="email"
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                        autoComplete="username"
                        required
                    />
                    {form.errors.email && (
                        <p className="mt-1 text-sm text-red-600">{form.errors.email}</p>
                    )}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700">Password</label>
                    <input
                        className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        type="password"
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        autoComplete="current-password"
                        required
                    />
                    {form.errors.password && (
                        <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>
                    )}
                </div>

                <label className="flex items-center gap-2 text-sm text-slate-700">
                    <input
                        type="checkbox"
                        checked={form.data.remember}
                        onChange={(e) => form.setData('remember', e.target.checked)}
                    />
                    Remember me
                </label>

                <button
                    type="submit"
                    className="w-full rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                    disabled={form.processing}
                >
                    {form.processing ? 'Signing in…' : 'Sign in'}
                </button>
            </form>
        </div>
    );
}
