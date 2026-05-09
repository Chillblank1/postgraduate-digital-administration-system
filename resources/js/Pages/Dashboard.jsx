import { Link, router, usePage } from '@inertiajs/react';

export default function Dashboard({ studentSubmissions, supervisorSubmissions }) {
    const { props } = usePage();
    const flash = props.flash ?? {};

    return (
        <div className="mx-auto max-w-5xl px-6 py-10">
            <div className="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-semibold">Dashboard</h1>
                    <p className="mt-1 text-sm text-slate-600">
                        Student workflow slice: draft → supervisor review → outcome.
                    </p>
                </div>

                <div className="flex items-center gap-3">
                    <Link className="text-sm text-slate-700 underline" href="/submissions/create">
                        New submission (student)
                    </Link>
                    <button
                        type="button"
                        className="text-sm text-red-700 underline"
                        onClick={() => router.post('/logout')}
                    >
                        Log out
                    </button>
                </div>
            </div>

            {(flash.success || flash.error) && (
                <div className="mb-6 rounded-md border border-slate-200 bg-white p-4 text-sm">
                    {flash.success && <p className="text-green-700">{flash.success}</p>}
                    {flash.error && <p className="text-red-700">{flash.error}</p>}
                </div>
            )}

            {studentSubmissions?.length > 0 && (
                <section className="mb-10">
                    <h2 className="mb-3 text-lg font-medium">My submissions</h2>
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <table className="min-w-full text-sm">
                            <thead className="bg-slate-50 text-left text-xs uppercase text-slate-600">
                                <tr>
                                    <th className="px-4 py-3">Title</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {studentSubmissions.map((s) => (
                                    <tr key={s.id} className="border-t border-slate-100">
                                        <td className="px-4 py-3">{s.title}</td>
                                        <td className="px-4 py-3 font-mono text-xs">{s.status}</td>
                                        <td className="px-4 py-3 text-right">
                                            <Link className="underline" href={`/submissions/${s.id}`}>
                                                Open
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            )}

            {supervisorSubmissions?.length > 0 && (
                <section>
                    <h2 className="mb-3 text-lg font-medium">Supervisor queue</h2>
                    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <table className="min-w-full text-sm">
                            <thead className="bg-slate-50 text-left text-xs uppercase text-slate-600">
                                <tr>
                                    <th className="px-4 py-3">Title</th>
                                    <th className="px-4 py-3">Student</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {supervisorSubmissions.map((s) => (
                                    <tr key={s.id} className="border-t border-slate-100">
                                        <td className="px-4 py-3">{s.title}</td>
                                        <td className="px-4 py-3">
                                            {s.student
                                                ? `${s.student.first_name} ${s.student.last_name}`
                                                : '—'}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">{s.status}</td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                className="underline"
                                                href={`/supervisor/submissions/${s.id}`}
                                            >
                                                Review
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            )}

            {(!studentSubmissions || studentSubmissions.length === 0) &&
                (!supervisorSubmissions || supervisorSubmissions.length === 0) && (
                    <p className="text-sm text-slate-600">
                        No rows to show for your role yet. Run{' '}
                        <span className="font-mono">php artisan migrate:fresh --seed</span> and sign in
                        with the seeded accounts from <span className="font-mono">SETUP.txt</span>.
                    </p>
                )}
        </div>
    );
}
