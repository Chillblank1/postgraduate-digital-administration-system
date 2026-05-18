import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { Card, CardContent} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DollarSign, Plus } from 'lucide-react';
import { IEvaluator } from '@/types';

export const DUMMY_Evaluators: IEvaluator[] = [
    {
        id: '1',
        firstName: 'John',
        lastName: 'Smith',
        email: 'john.smith@university.edu',
        type: 'internal',
        status: 'Active',
        departmentId: 'dept1',
    },
    {
        id: '2',
        firstName: 'Prof.',
        lastName: 'Haufiku',
        email: 'haufiku@university.edu',
        type: 'external',
        status: 'Active',
        institution: 'External University',
    },
    {
        id: '3',
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane.doe@university.edu',
        type: 'internal',
        status: 'Active',
        departmentId: 'dept2',
    },
    {
        id: '4',
        firstName: 'Dr.',
        lastName: 'Kavari',
        email: 'kavari@university.edu',
        type: 'external',
        status: 'Active',
        institution: 'External University',
    }
];

const statusStyles: Record<string, string> = {
    Pending: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    Approved: 'bg-green-500/20 text-green-400 border-green-500/30',
    Rejected: 'bg-red-500/20 text-red-400 border-red-500/30',
    Active: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
};

type Props = {
    evaluators?: IEvaluator[];
};

export default function Evaluators({ evaluators = DUMMY_Evaluators }: Props) {
    const [statusFilter, setStatusFilter] = useState('all');

    const filtered = statusFilter === 'all'
        ? evaluators
        : evaluators.filter(e => e.status === statusFilter);

    const internalEvaluators = filtered.filter(e => e.type === 'internal');
    const externalEvaluators = filtered.filter(e => e.type === 'external');

    const EvaluatorTable = ({ 
        evaluators, 
        title, 
        isExternal = false 
    }: { 
        evaluators: IEvaluator[], 
        title: string,
        isExternal?: boolean
    }) => (
        <div className="flex flex-col h-full">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-semibold">{title}</h2>
                {isExternal && (
                    <Button 
                        size="sm" 
                        className="gap-2 bg-blue-600 hover:bg-blue-700 text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Propose
                    </Button>
                )}
            </div>
            <Card className="flex-1 flex flex-col">
                <CardContent className="p-0 flex-1 flex flex-col">
                    <div className="overflow-x-auto flex-1">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-muted/50">
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Evaluator
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        {isExternal ? 'Institution' : 'Department'}
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {evaluators.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="px-4 py-8 text-center text-muted-foreground">
                                            No {isExternal ? 'external' : 'internal'} evaluators found
                                        </td>
                                    </tr>
                                ) : (
                                    evaluators.map((evaluator) => (
                                        <tr key={evaluator.id} className="hover:bg-muted/30 transition-colors">
                                            <td className="px-4 py-3 text-sm font-medium">
                                                <div className="flex flex-col">
                                                    <span>{evaluator.firstName} {evaluator.lastName}</span>
                                                    <span className="text-xs text-muted-foreground">{evaluator.email}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-sm text-muted-foreground">
                                                {isExternal 
                                                    ? evaluator.title || '—' 
                                                    : evaluator.specialization || '—'}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge 
                                                    variant="outline" 
                                                    className={statusStyles[evaluator.status] ?? ''}
                                                >
                                                    {evaluator.status}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex gap-2">
                                                    {evaluator.status === 'Pending' && (
                                                        <>
                                                            <Button 
                                                                size="sm" 
                                                                variant="outline"
                                                                className="border-green-500/30 text-green-400 hover:bg-green-500/20"
                                                            >
                                                                Approve
                                                            </Button>
                                                            <Button 
                                                                size="sm" 
                                                                variant="outline"
                                                                className="border-red-500/30 text-red-400 hover:bg-red-500/20"
                                                            >
                                                                Reject
                                                            </Button>
                                                        </>
                                                    )}
                                                    <Button size="sm" variant="outline">
                                                        View
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
    );

    return (
        <>
            <Head title="Evaluators" />
            <div className="p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold">Evaluators</h1>
                    <p className="text-sm text-muted-foreground">
                        Internal and External Evaluator management
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
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Pending">Pending</SelectItem>
                            <SelectItem value="Approved">Approved</SelectItem>
                            <SelectItem value="Rejected">Rejected</SelectItem>
                        </SelectContent>
                    </Select>
                    <span className="text-sm text-muted-foreground">
                        {filtered.length} total
                    </span>
                </div>

                {/* Two Column Layout */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <EvaluatorTable 
                        evaluators={internalEvaluators} 
                        title="Internal Evaluators"
                        isExternal={false}
                    />
                    <EvaluatorTable 
                        evaluators={externalEvaluators} 
                        title="External Evaluators"
                        isExternal={true}
                    />
                </div>
            </div>
        </>
    );
}

Evaluators.layout = {
    title: 'Evaluators',
    description: 'Evaluators Management',
};
