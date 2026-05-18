import { Form, Head, Link, useForm } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type EvalOption = {
    id: number;
    name: string;
    email: string;
};

type SubmissionPayload = {
    id: number;
    type: string;
    title: string | null;
    status: string;
    student: { name: string; email: string } | null;
    supervisor: { name: string; email: string } | null;
    thesis_evaluations: Array<{
        evaluator: string | null;
        evaluator_type: string;
        total_marks: number | null;
        percentage: number | null;
        recommendation: string | null;
        status: string;
    }>;
    submission_evaluations: Array<{
        evaluator: string | null;
        evaluator_type: string;
        grade: string | null;
        notes: string | null;
        status: string;
    }>;
    evaluator_assignments: Array<{
        evaluator: string | null;
        deadline: string | null;
        status: string;
    }>;
    external_examiner_proposals: Array<{
        examiner_name: string;
        institution: string | null;
        status: string;
    }>;
};

export default function HodSubmission({
    submission,
    internal_evaluators,
}: {
    submission: SubmissionPayload;
    internal_evaluators: EvalOption[];
}) {
    const assignForm = useForm({
        evaluator_id: internal_evaluators[0]?.id?.toString() ?? '',
        deadline: '',
    });

    const proposalForm = useForm({
        examiner_name: '',
        examiner_email: '',
        institution: '',
        motivation: '',
    });

    const forwardForm = useForm({});

    const isThesis = submission.type.toLowerCase().includes('thesis');

    return (
        <>
            <Head title={`HoD · ${submission.title ?? 'Submission'}`} />

            <div className="flex flex-col gap-8 p-4">
                <div>
                    <Heading
                        variant="small"
                        title={submission.title ?? 'Untitled submission'}
                        description={`${submission.type} · ${submission.status}`}
                    />
                    <p className="mt-2 text-sm text-muted-foreground">
                        Student: {submission.student?.name ?? '—'} · Supervisor:{' '}
                        {submission.supervisor?.name ?? '—'}
                    </p>
                    <Button asChild className="mt-3" variant="outline" size="sm">
                        <Link href="/hod">← Back to list</Link>
                    </Button>
                </div>

                <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">HD-02 · Assign internal evaluator</h2>
                    <Form
                        onSubmit={(e) => {
                            e.preventDefault();
                            assignForm.post(
                                `/hod/submissions/${submission.id}/internal-evaluators`,
                            );
                        }}
                        className="max-w-xl space-y-4"
                    >
                        <div>
                            <Label htmlFor="evaluator_id">Internal evaluator</Label>
                            <select
                                id="evaluator_id"
                                name="evaluator_id"
                                className="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                value={assignForm.data.evaluator_id}
                                onChange={(e) =>
                                    assignForm.setData('evaluator_id', e.target.value)
                                }
                            >
                                {internal_evaluators.length === 0 ? (
                                    <option value="">No evaluators in your department</option>
                                ) : (
                                    internal_evaluators.map((u) => (
                                        <option key={u.id} value={u.id}>
                                            {u.name} ({u.email})
                                        </option>
                                    ))
                                )}
                            </select>
                            <InputError message={assignForm.errors.evaluator_id} />
                        </div>
                        <div>
                            <Label htmlFor="deadline">Deadline (optional)</Label>
                            <Input
                                id="deadline"
                                type="datetime-local"
                                value={assignForm.data.deadline}
                                onChange={(e) =>
                                    assignForm.setData('deadline', e.target.value)
                                }
                            />
                            <InputError message={assignForm.errors.deadline} />
                        </div>
                        <Button disabled={assignForm.processing} type="submit">
                            Assign evaluator
                        </Button>
                    </Form>
                </section>

                {isThesis && (
                    <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <h2 className="mb-3 text-lg font-semibold">
                            HD-03 · Propose external examiner
                        </h2>
                        <Form
                            onSubmit={(e) => {
                                e.preventDefault();
                                proposalForm.post(
                                    `/hod/submissions/${submission.id}/external-examiner-proposals`,
                                );
                            }}
                            className="max-w-xl space-y-4"
                        >
                            <div>
                                <Label htmlFor="examiner_name">Examiner name</Label>
                                <Input
                                    id="examiner_name"
                                    value={proposalForm.data.examiner_name}
                                    onChange={(e) =>
                                        proposalForm.setData(
                                            'examiner_name',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError message={proposalForm.errors.examiner_name} />
                            </div>
                            <div>
                                <Label htmlFor="examiner_email">Email (optional)</Label>
                                <Input
                                    id="examiner_email"
                                    type="email"
                                    value={proposalForm.data.examiner_email}
                                    onChange={(e) =>
                                        proposalForm.setData(
                                            'examiner_email',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError message={proposalForm.errors.examiner_email} />
                            </div>
                            <div>
                                <Label htmlFor="institution">Institution (optional)</Label>
                                <Input
                                    id="institution"
                                    value={proposalForm.data.institution}
                                    onChange={(e) =>
                                        proposalForm.setData(
                                            'institution',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError message={proposalForm.errors.institution} />
                            </div>
                            <div>
                                <Label htmlFor="motivation">Motivation (optional)</Label>
                                <textarea
                                    id="motivation"
                                    className="mt-1 min-h-[88px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={proposalForm.data.motivation}
                                    onChange={(e) =>
                                        proposalForm.setData(
                                            'motivation',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError message={proposalForm.errors.motivation} />
                            </div>
                            <Button disabled={proposalForm.processing} type="submit">
                                Submit proposal to FPGC queue
                            </Button>
                        </Form>
                    </section>
                )}

                <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">
                        HD-04 · Forward to FPGC-R / HDC preparation
                    </h2>
                    <Form
                        onSubmit={(e) => {
                            e.preventDefault();
                            forwardForm.post(
                                `/hod/submissions/${submission.id}/forward-fpgc-r`,
                            );
                        }}
                        className="flex flex-wrap items-center gap-3"
                    >
                        <Button disabled={forwardForm.processing} type="submit">
                            Forward package
                        </Button>
                    </Form>
                </section>

                <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">
                        HD-05 · Evaluator grades &amp; structured evaluations
                    </h2>

                    <h3 className="mt-4 mb-2 text-sm font-semibold">Submission evaluations</h3>
                    {submission.submission_evaluations.length === 0 ? (
                        <p className="text-sm text-muted-foreground">None recorded.</p>
                    ) : (
                        <ul className="space-y-2 text-sm">
                            {submission.submission_evaluations.map((ev, i) => (
                                <li key={i} className="rounded border border-border p-2">
                                    <div className="font-medium">{ev.evaluator ?? '—'}</div>
                                    <div className="text-muted-foreground">
                                        Type: {ev.evaluator_type} · Grade: {ev.grade ?? '—'} ·
                                        Status: {ev.status}
                                    </div>
                                    {ev.notes && (
                                        <p className="mt-1 text-muted-foreground">{ev.notes}</p>
                                    )}
                                </li>
                            ))}
                        </ul>
                    )}

                    <h3 className="mt-6 mb-2 text-sm font-semibold">Thesis evaluations</h3>
                    {submission.thesis_evaluations.length === 0 ? (
                        <p className="text-sm text-muted-foreground">None recorded.</p>
                    ) : (
                        <ul className="space-y-2 text-sm">
                            {submission.thesis_evaluations.map((ev, i) => (
                                <li key={i} className="rounded border border-border p-2">
                                    <div className="font-medium">{ev.evaluator ?? '—'}</div>
                                    <div className="text-muted-foreground">
                                        Marks: {ev.total_marks ?? '—'} · %:{' '}
                                        {ev.percentage ?? '—'} · Recommendation:{' '}
                                        {ev.recommendation ?? '—'} · Status: {ev.status}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>

                <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">Assignments snapshot</h2>
                    {submission.evaluator_assignments.length === 0 ? (
                        <p className="text-sm text-muted-foreground">No evaluator assignments.</p>
                    ) : (
                        <ul className="space-y-2 text-sm">
                            {submission.evaluator_assignments.map((a, i) => (
                                <li key={i}>
                                    {a.evaluator ?? '—'} — deadline {a.deadline ?? '—'} —{' '}
                                    {a.status}
                                </li>
                            ))}
                        </ul>
                    )}
                </section>

                <section className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">External examiner proposals</h2>
                    {submission.external_examiner_proposals.length === 0 ? (
                        <p className="text-sm text-muted-foreground">None yet.</p>
                    ) : (
                        <ul className="space-y-2 text-sm">
                            {submission.external_examiner_proposals.map((p, i) => (
                                <li key={i}>
                                    {p.examiner_name} {p.institution ? `(${p.institution})` : ''}{' '}
                                    — {p.status}
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </div>
        </>
    );
}

HodSubmission.layout = {
    breadcrumbs: [
        { title: 'HoD workspace', href: '/hod' },
        { title: 'Submission', href: '#' },
    ],
};
