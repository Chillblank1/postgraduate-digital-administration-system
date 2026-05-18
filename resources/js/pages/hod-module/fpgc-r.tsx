import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Users } from 'lucide-react';
import { IFacultyPostgradRep } from '@/types/hod-types/fpgc-r';


const DUMMY_FacultyPostgradReps: IFacultyPostgradRep[] = [
    {
        id: '1',
        name: 'Dr. Sarah Johnson',
        email: 'sarah.johnson@university.edu',
        faculty: 'Engineering',
        department: 'Computer Science',
        status: 'Active',
    },
    {
        id: '2',
        name: 'Prof. James Okafor',
        email: 'j.okafor@university.edu',
        faculty: 'Engineering',
        department: 'Mechanical Engineering',
        status: 'Active',
    },
    {
        id: '3',
        name: 'Dr. Maria Santos',
        email: 'maria.santos@university.edu',
        faculty: 'Natural Sciences',
        department: 'Biology',
        status: 'Active',
    },
    {
        id: '4',
        name: 'Prof. Robert Chen',
        email: 'r.chen@university.edu',
        faculty: 'Natural Sciences',
        department: 'Chemistry',
        status: 'Inactive',
    },
    {
        id: '5',
        name: 'Dr. Alice Mwase',
        email: 'alice.mwase@university.edu',
        faculty: 'Business',
        department: 'Economics',
        status: 'Active',
    },
];

const statusStyles: Record<string, string> = {
    Active: 'bg-green-500/20 text-green-400 border-green-500/30',
    Inactive: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
};

type Props = {
    reps?: IFacultyPostgradRep[];
};

export default function FacultyPostgradReps({ reps = DUMMY_FacultyPostgradReps }: Props) {
    const [statusFilter, setStatusFilter] = useState('all');

    const filtered = statusFilter === 'all'
        ? reps
        : reps.filter(rep => rep.status === statusFilter);

    return (
        <>
            <Head title="Faculty Postgraduate Representatives" />
            <div className="p-6 space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Faculty Postgraduate Representatives</h1>
                        <p className="text-sm text-muted-foreground">
                            Postgraduate representatives across all faculties
                        </p>
                    </div>
                </div>

                {/* Filter */}
                <div className="flex items-center gap-3">
                    <Users className="h-4 w-4 text-muted-foreground" />
                    <Select value={statusFilter} onValueChange={setStatusFilter}>
                        <SelectTrigger className="w-48">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All statuses</SelectItem>
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Inactive">Inactive</SelectItem>
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
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Faculty
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">
                                            Department
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
                                    {filtered.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                                No representatives found
                                            </td>
                                        </tr>
                                    ) : (
                                        filtered.map((rep) => (
                                            <tr key={rep.id} className="hover:bg-muted/30 transition-colors">
                                                <td className="px-4 py-3 text-sm font-medium">
                                                    {rep.name}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground">
                                                    {rep.email}
                                                </td>
                                                <td className="px-4 py-3 text-sm">
                                                    {rep.faculty}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">
                                                    {rep.department}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <Badge 
                                                        variant="outline" 
                                                        className={statusStyles[rep.status] ?? ''}
                                                    >
                                                        {rep.status}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex gap-2">
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
        </>
    );
}

FacultyPostgradReps.layout = {
    title: 'Faculty Postgraduate Representatives',
    description: 'Faculty Postgraduate Representatives Management',
};