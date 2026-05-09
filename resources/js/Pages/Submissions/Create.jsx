import { Link, useForm } from '@inertiajs/react';

export default function Create({ demoSupervisorId }) {
    const form = useForm({
        supervisor_id: demoSupervisorId ?? '',
        co_supervisor_id: '',
        type: 'thesis',
        title: '',
        academic_level: 'masters',
    });

    return (
        <div className="mx-auto max-w-3xl px-6 py-10">
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-semibold">New submission</h1>
                <Link className="text-sm underline" href="/dashboard">
                    Back
                </Link>
            </div>

            <form
                className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                onSubmit={(e) => {
                    e.preventDefault();
                    form.post('/submissions');
                }}
            >
                <p className="text-sm text-slate-600">
                    Supervisor ID defaults to the first seeded supervisor ({String(demoSupervisorId ?? 'n/a')}).
                </p>

                <div className="grid gap-4 md:grid-cols-2">
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Supervisor ID</label>
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
                        <label className="block text-sm font-medium text-slate-700">Co-supervisor ID</label>
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
                    {form.errors.type && <p className="mt-1 text-sm text-red-600">{form.errors.type}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700">Title</label>
                    <input
                        className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        value={form.data.title}
                        onChange={(e) => form.setData('title', e.target.value)}
                        required
                    />
                    {form.errors.title && <p className="mt-1 text-sm text-red-600">{form.errors.title}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700">Academic level</label>
                    <input
                        className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                        value={form.data.academic_level}
                        onChange={(e) => form.setData('academic_level', e.target.value)}
                    />
                    {form.errors.academic_level && (
                        <p className="mt-1 text-sm text-red-600">{form.errors.academic_level}</p>
                    )}
                </div>

                <button
                    type="submit"
                    className="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                    disabled={form.processing}
                >
                    {form.processing ? 'Saving…' : 'Save draft'}
                </button>
            </form>
        </div>
    );
}
