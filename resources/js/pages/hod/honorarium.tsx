import { Head, Link, useForm } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';

type ClaimRow = {
    id: number;
    status: string;
    claim_file_key: string | null;
    submission: { id: number; title: string | null; type: string } | null;
    evaluator: { name: string; email: string } | null;
    student: { name: string; email: string } | null;
    created_at: string | null;
};

function ClaimActions({ claimId }: { claimId: number }) {
    const approve = useForm({ decision: 'approved' as const });
    const reject = useForm({ decision: 'rejected' as const });

    return (
        <div className="flex gap-2">
            <Button
                size="sm"
                variant="default"
                disabled={approve.processing}
                onClick={() => approve.patch(`/hod/honorarium-claims/${claimId}`)}
            >
                Approve
            </Button>
            <Button
                size="sm"
                variant="destructive"
                disabled={reject.processing}
                onClick={() => reject.patch(`/hod/honorarium-claims/${claimId}`)}
            >
                Reject
            </Button>
        </div>
    );
}

export default function HodHonorarium({ claims }: { claims: ClaimRow[] }) {
    return (
        <>
            <Head title="HoD · Honorarium claims" />

            <div className="flex flex-col gap-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        variant="small"
                        title="Honorarium claims (HD-06)"
                        description="Approve or reject claims from external evaluators tied to submissions in your department."
                    />
                    <Button asChild variant="outline" size="sm">
                        <Link href="/hod">← HoD home</Link>
                    </Button>
                </div>

                {claims.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                        No pending or submitted claims for your department.
                    </p>
                ) : (
                    <div className="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="p-2 font-medium">Submission</th>
                                    <th className="p-2 font-medium">Evaluator</th>
                                    <th className="p-2 font-medium">Student</th>
                                    <th className="p-2 font-medium">Status</th>
                                    <th className="p-2 font-medium">File key</th>
                                    <th className="p-2 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {claims.map((c) => (
                                    <tr key={c.id} className="border-b border-border/60">
                                        <td className="p-2">
                                            {c.submission?.title ?? '—'}
                                            <div className="text-xs text-muted-foreground">
                                                {c.submission?.type}
                                            </div>
                                        </td>
                                        <td className="p-2">{c.evaluator?.name ?? '—'}</td>
                                        <td className="p-2">{c.student?.name ?? '—'}</td>
                                        <td className="p-2">{c.status}</td>
                                        <td className="p-2 text-xs text-muted-foreground">
                                            {c.claim_file_key ?? '—'}
                                        </td>
                                        <td className="p-2">
                                            <ClaimActions claimId={c.id} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </>
    );
}

HodHonorarium.layout = {
    breadcrumbs: [
        { title: 'HoD workspace', href: '/hod' },
        { title: 'Honorarium claims', href: '/hod/honorarium-claims' },
    ],
};
