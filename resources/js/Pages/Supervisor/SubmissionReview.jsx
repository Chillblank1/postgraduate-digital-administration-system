import { Link, useForm } from '@inertiajs/react';

export default function SubmissionReview({ submission, canReview }) {
    const form = useForm({
        decision: 'approve',
        supervisor_feedback: '',
        comments: '',
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
                <h2 className="mb-2 text-sm font-semibold text-slate-700">Student</h2>
                <p className="text-sm">
                    {submission.student
                        ? `${submission.student.first_name} ${submission.student.last_name}`
                        : '—'}
                </p>
            </div>

            {!canReview && (
                <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    This submission is not awaiting supervisor review (or you are not eligible to review it).
                </div>
            )}

            {canReview && (
                <form
                    className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post(`/supervisor/submissions/${submission.id}/review`);
                    }}
                >
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Decision</label>
                        <select
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.decision}
                            onChange={(e) => form.setData('decision', e.target.value)}
                        >
                            <option value="approve">Approve</option>
                            <option value="revision">Request revision</option>
                            <option value="reject">Reject</option>
                        </select>
                        {form.errors.decision && (
                            <p className="mt-1 text-sm text-red-600">{form.errors.decision}</p>
                        )}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Feedback</label>
                        <textarea
                            className="mt-1 min-h-[120px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.supervisor_feedback}
                            onChange={(e) => form.setData('supervisor_feedback', e.target.value)}
                            placeholder="Written feedback for the student…"
                        />
                        {form.errors.supervisor_feedback && (
                            <p className="mt-1 text-sm text-red-600">{form.errors.supervisor_feedback}</p>
                        )}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">
                            Internal workflow comments (optional)
                        </label>
                        <textarea
                            className="mt-1 min-h-[80px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                            value={form.data.comments}
                            onChange={(e) => form.setData('comments', e.target.value)}
                        />
                        {form.errors.comments && (
                            <p className="mt-1 text-sm text-red-600">{form.errors.comments}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        className="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                        disabled={form.processing}
                    >
                        {form.processing ? 'Saving…' : 'Submit review'}
                    </button>
                </form>
            )}
        </div>
    );
}
