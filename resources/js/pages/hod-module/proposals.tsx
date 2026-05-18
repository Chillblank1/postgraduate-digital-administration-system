import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { IProposal } from '@/types/hod-types/proposal';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FileText } from 'lucide-react';

export const DUMMY_PROPOSASLS: IProposal[] = [
    {
        id: '1',
        studentName: 'John Doe',
        title: 'AI in Healthcare',
        supervisor: 'Dr. Smith',
        type: 'PhD',
        evaluator: 'Dr. Brown',
        status: 'Under Review',
        submissionDate: '2024-05-01',
    },
    {
        id: '2',
        studentName: 'Susan Doe',
        title: 'AI in Sports',
        supervisor: 'Dr. Matheus',
        type: 'PhD',
        evaluator: null,
        status: 'Pending',
        submissionDate: '2024-05-01',
    },
    {
        id: '3',
        studentName: 'Jane Doe',
        title: 'AI in Education',
        supervisor: 'Dr. Smith',
        type: 'MSc',
        evaluator: 'Dr. Brown',
        status: 'Approved',
        submissionDate: '2024-05-01',
    },
];

const statusStyles: Record<string, string> = {
    Pending: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    'Under Review': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    Approved: 'bg-green-500/20 text-green-400 border-green-500/30',
    'Sent to FPGC-R': 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    Rejected: 'bg-red-500/20 text-red-400 border-red-500/30',
};

type Props = {
    proposals?: IProposal[];
};

export default function Proposals({ proposals = DUMMY_PROPOSASLS }: Props) {
    const [statusFilter, setStatusFilter] = useState('all');

    const filtered = statusFilter === 'all'
        ? proposals
        : proposals.filter(p => p.status === statusFilter);

    return (
        <>
            <Head title="Proposals" />
            <div className="p-6 space-y-6">

                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold">Student Proposals</h1>
                    <p className="text-sm text-muted-foreground">
                        SoPs and theses from supervisors
                    </p>
                </div>

                {/* Filter */}
                <div className="flex items-center gap-3">
                    <FileText className="h-4 w-4 text-muted-foreground" />
                    <Select value={statusFilter} onValueChange={setStatusFilter}>
                        <SelectTrigger className="w-48">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All statuses</SelectItem>
                            <SelectItem value="Pending">Pending</SelectItem>
                            <SelectItem value="Under Review">Under Review</SelectItem>
                            <SelectItem value="Approved">Approved</SelectItem>
                            <SelectItem value="Sent to FPGC-R">Sent to FPGC-R</SelectItem>
                            <SelectItem value="Rejected">Rejected</SelectItem>
                        </SelectContent>
                    </Select>
                    <span className="text-sm text-muted-foreground">
                        {filtered.length} result{filtered.length !== 1 ? 's' : ''}
                    </span>
                </div>

                {/* Table */}
                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-border bg-muted/50">
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Student</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Title</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Supervisor</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden lg:table-cell">Type</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Evaluator</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {filtered.length === 0 ? (
                                        <tr>
                                            <td colSpan={7} className="px-4 py-8 text-center text-muted-foreground">
                                                No proposals found
                                            </td>
                                        </tr>
                                    ) : (
                                        filtered.map((proposal) => (
                                            <tr key={proposal.id} className="hover:bg-muted/30 transition-colors">
                                                <td className="px-4 py-3 text-sm font-medium">{proposal.studentName}</td>
                                                <td className="px-4 py-3 text-sm max-w-xs truncate">{proposal.title}</td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">{proposal.supervisor}</td>
                                                <td className="px-4 py-3 hidden lg:table-cell">
                                                    <Badge variant="outline">{proposal.type}</Badge>
                                                </td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">
                                                    {proposal.evaluator ?? <span className="text-muted-foreground/50">—</span>}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <Badge variant="outline" className={statusStyles[proposal.status] ?? ''}>
                                                        {proposal.status}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3">
                                                    {proposal.status === 'Pending' ? (
                                                        <Button size="sm" className="bg-green-600 hover:bg-green-700 text-white">
                                                            Assign
                                                        </Button>
                                                    ) : (
                                                        <Button size="sm" variant="outline">
                                                            View
                                                        </Button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

            </div>
        </>
    );
}