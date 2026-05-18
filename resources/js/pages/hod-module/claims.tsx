import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { IClaim } from '@/types/hod-types/claim';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DollarSign } from 'lucide-react';

export const DUMMY_CLAIMS: IClaim[] = [
    {
        id: '1',
        evaluatorName: 'Dr. John Smith',
        proposalTitle: 'Advanced Machine Learning Research',
        date: '2024-05-10',
        amount: 500,
        status: 'Pending',
    },
    {
        id: '2',
        evaluatorName: 'Prof. Haufiku',
        proposalTitle: 'Digital Transformation in Public Sector',
        date: '2024-04-20',
        amount: 4000,
        status: 'Approved',
    },
    {
        id: '3',
        evaluatorName: 'Dr. Iipinge',
        proposalTitle: 'Water Resource Management',
        date: '2024-05-13',
        amount: 3500,
        status: 'Rejected',
    },
];

const statusStyles: Record<string, string> = {
    Pending: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    Approved: 'bg-green-500/20 text-green-400 border-green-500/30',
    Rejected: 'bg-red-500/20 text-red-400 border-red-500/30',
};

type Props = {
    claims?: IClaim[];
};

export default function Claims({ claims = DUMMY_CLAIMS }: Props) {
    const [statusFilter, setStatusFilter] = useState('all');

    const filtered = statusFilter === 'all'
        ? claims
        : claims.filter(c => c.status === statusFilter);

    return (
        <>
            <Head title="Honorarium Claims" />
            <div className="p-6 space-y-6">

                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold">Honorarium Claims</h1>
                    <p className="text-sm text-muted-foreground">
                        Claims from external evaluators
                    </p>
                </div>

                {/* Filter */}
                <div className="flex items-center gap-3">
                    <DollarSign className="h-4 w-4 text-muted-foreground" />
                    <Select value={statusFilter} onValueChange={setStatusFilter}>
                        <SelectTrigger className="w-48">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All statuses</SelectItem>
                            <SelectItem value="Pending">Pending</SelectItem>
                            <SelectItem value="Approved">Approved</SelectItem>
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
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Evaluator</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Proposal</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Date</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {filtered.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                                No claims found
                                            </td>
                                        </tr>
                                    ) : (
                                        filtered.map((claim) => (
                                            <tr key={claim.id} className="hover:bg-muted/30 transition-colors">
                                                <td className="px-4 py-3 text-sm font-medium">{claim.evaluatorName}</td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell max-w-xs truncate">{claim.proposalTitle}</td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">{claim.date}</td>
                                                <td className="px-4 py-3 text-sm font-medium">N${claim.amount.toLocaleString()}</td>
                                                <td className="px-4 py-3">
                                                    <Badge variant="outline" className={statusStyles[claim.status] ?? ''}>
                                                        {claim.status}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex gap-2">
                                                        {claim.status === 'Pending' && (
                                                            <>
                                                                <Button size="sm" variant="outline"
                                                                    className="border-green-500/30 text-green-400 hover:bg-green-500/20">
                                                                    Approve
                                                                </Button>
                                                                <Button size="sm" variant="outline"
                                                                    className="border-red-500/30 text-red-400 hover:bg-red-500/20">
                                                                    Reject
                                                                </Button>
                                                            </>
                                                        )}
                                                        <Button size="sm" variant="outline">
                                                            Receipt
                                                        </Button>
                                                    </div>
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

Claims.layout = {
    title: 'Claims',
    description: 'Honorarium Claims Management',
};