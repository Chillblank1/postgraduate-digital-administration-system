import { Link, router, useForm } from '@inertiajs/react';

export default function Show({ submission, canUpdate, canSubmit }) {
    const form = useForm({
        id: submission.id,
        supervisor_id: submission.supervisor_id,
        co_supervisor_id: submission.co_supervisor_id ?? '',
        type: submission.type,
        title: submission.title,
        academic_level: submission.academic_level ?? '',
    });

    return (
        <div className="mx-auto max-w-3xl px-6 py-10">
            <div className="mb-6 flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold">{submission.title}</h1>
                    <p className="mt-1 font-mono text-xs text-slate-600">{submission.status}</p>
                </div>
                <Link className="text-sm underline" href="/dashboard">
                    Dashboard
                </Link>
            </div>

            <div className="mb-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <dl className="grid gap-3 text-sm md:grid-cols-2">
                    <div>
                        <dt className="text-slate-600">Type</dt>
                        <dd className="font-medium">{submission.type}</dd>
                    </div>
                    <div>
                        <dt className="text-slate-600">Submitted at</dt>
                        <dd className="font-medium">{submission.submitted_at ?? '—'}</dd>
                    </div>
                    <div className="md:col-span-2">
                        <dt className="text-slate-600">Supervisor decision</dt>
                        <dd className="font-medium">{submission.supervisor_decision ?? '—'}</dd>
                    </div>
                    <div className="md:col-span-2">
                        <dt className="text-slate-600">Supervisor feedback</dt>
                        <dd className="whitespace-pre-wrap font-medium">
                            {submission.supervisor_feedback ?? '—'}
                        </dd>
                    </div>
                </dl>
            </div>

            {canUpdate && (
                <form
                    className="mb-6 space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post('/submissions');
                    }}
                >
                    <h2 className="text-lg font-medium">Edit draft</h2>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <label className="block text-sm font-medium text-slate-700">
                                Supervisor ID
                            </label>
                            <input
                                className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                                type="number"
                                value={form.data.supervisor_id}
                                onChange={(e) => form.setData('supervisor_id', Number(e.target.value))}
                                required
                            />
                            {form.errors.supervisor_id && (
                                <p className="mt-1 text-sm text-red-600">{form.errors.supervisor_id}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700">
                                Co-supervisor ID
                            </label>
                            <input
                                className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                                type="number"
                                value={form.data.co_supervisor_id}
                                onChange={(e) =>
                                    form.setData(
                                        'co_supervisor_id',
                                        e.target.value === '' ? '' : Number(e.target.value),
                                    )
                                }
                                placeholder="Optional"
                            />
                            {form.errors.co_supervisor_id && (
                                <p className="mt-1 text-sm text-red-600">{form.errors.co_supervisor_id}</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Type</label>
                        <select
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.type}
                            onChange={(e) => form.setData('type', e.target.value)}
                        >
                            <option value="thesis">thesis</option>
                            <option value="sop">sop</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Title</label>
                        <input
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.title}
                            onChange={(e) => form.setData('title', e.target.value)}
                            required
                        />
                        {form.errors.title && (
                            <p className="mt-1 text-sm text-red-600">{form.errors.title}</p>
                        )}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Academic level</label>
                        <input
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.academic_level}
                            onChange={(e) => form.setData('academic_level', e.target.value)}
                        />
                    </div>

                    <button
                        type="submit"
                        className="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                        disabled={form.processing}
                    >
                        {form.processing ? 'Saving…' : 'Save draft'}
                    </button>
                </form>
            )}

            {canSubmit && (
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="mb-2 text-lg font-medium">Submit for supervisor review</h2>
                    <p className="mb-4 text-sm text-slate-600">
                        This executes the workflow transition and notifies your supervisor.
                    </p>
                    <button
                        type="button"
                        className="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 disabled:opacity-50"
                        onClick={() => router.post(`/submissions/${submission.id}/submit`)}
                    >
                        Submit
                    </button>
                </div>
            )}
        </div>
    );
}
