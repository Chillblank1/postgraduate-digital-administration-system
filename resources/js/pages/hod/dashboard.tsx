import { Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';

type SubmissionRow = {
    id: number;
    type: string;
    title: string | null;
    status: string;
    student: { name: string; email: string } | null;
    supervisor: { name: string; email: string } | null;
    updated_at: string | null;
};

export default function HodDashboard({
    department,
    submissions,
    pending_honorarium_claims,
}: {
    department: { name: string; faculty: string | null } | null;
    submissions: SubmissionRow[];
    pending_honorarium_claims: number;
}) {
    return (
        <>
            <Head title="HoD workspace" />

            <div className="flex flex-col gap-6 p-4">
                <Heading
                    variant="small"
                    title="Head of Department workspace"
                    description={
                        department
                            ? `${department.name}${department.faculty ? ` · ${department.faculty}` : ''}`
                            : 'Department not set on your HoD profile.'
                    }
                />

                <div className="flex flex-wrap gap-3">
                    <Button asChild variant="outline">
                        <Link href="/hod/honorarium-claims">
                            Honorarium claims ({pending_honorarium_claims} pending)
                        </Link>
                    </Button>
                </div>

                <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <h2 className="mb-3 text-lg font-semibold">Incoming submissions (HD-01)</h2>
                    <p className="mb-4 text-sm text-muted-foreground">
                        Filter via query{' '}
                        <code className="rounded bg-muted px-1 py-0.5 text-xs">
                            ?types=sop,thesis
                        </code>{' '}
                        — defaults match common SoP / thesis labels.
                    </p>

                    {submissions.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            No submissions in queue for your department yet.
                        </p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-border">
                                        <th className="p-2 font-medium">Title</th>
                                        <th className="p-2 font-medium">Type</th>
                                        <th className="p-2 font-medium">Status</th>
                                        <th className="p-2 font-medium">Student</th>
                                        <th className="p-2 font-medium">Supervisor</th>
                                        <th className="p-2 font-medium" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {submissions.map((s) => (
                                        <tr
                                            key={s.id}
                                            className="border-b border-border/60"
                                        >
                                            <td className="p-2">{s.title ?? '—'}</td>
                                            <td className="p-2">{s.type}</td>
                                            <td className="p-2">{s.status}</td>
                                            <td className="p-2">{s.student?.name ?? '—'}</td>
                                            <td className="p-2">{s.supervisor?.name ?? '—'}</td>
                                            <td className="p-2 text-right">
                                                <Button asChild size="sm" variant="secondary">
                                                    <Link href={`/hod/submissions/${s.id}`}>
                                                        Open (HD-02…05)
                                                    </Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

HodDashboard.layout = {
    breadcrumbs: [
        {
            title: 'HoD workspace',
            href: '/hod',
        },
    ],
};
