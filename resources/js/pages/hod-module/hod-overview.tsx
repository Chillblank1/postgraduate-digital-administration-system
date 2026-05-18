import { IClaim } from "@/types/hod-types/claim";
import { IProposal } from "@/types/hod-types/proposal";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { DUMMY_CLAIMS } from "./claims";
import { DUMMY_PROPOSASLS } from "./proposals";
import {
    AlertCircle,
    ClipboardCheck,
    Send,
    DollarSign,
    ArrowRight,
} from "lucide-react";

const statusStyles: Record<string, string> = {
    'Pending': 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    'Under Review': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    'Approved': 'bg-green-500/20 text-green-400 border-green-500/30',
    'Sent to FPGC-R': 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    'Rejected': 'bg-red-500/20 text-red-400 border-red-500/30',
};

type StatCardProps = {
    title: string;
    value: number;
    description: string;
    icon: React.ElementType;
    accent: string;
};

function StatCard({ title, value, description, icon: Icon, accent }: StatCardProps) {
    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">
                    {title}
                </CardTitle>
                <div className={`rounded-md p-2 ${accent}`}>
                    <Icon className="h-4 w-4 text-white" />
                </div>
            </CardHeader>
            <CardContent>
                <div className="text-3xl font-bold">{value}</div>
                <p className="mt-1 text-xs text-muted-foreground">{description}</p>
            </CardContent>
        </Card>
    );
}

export default function HODOverview() {
    const claims = DUMMY_CLAIMS;
    const proposals = DUMMY_PROPOSASLS;

    const pendingCount = proposals.filter(p => p.status === 'Pending').length;
    const underReviewCount = proposals.filter(p => p.status === 'Under Review').length;
    const sentToFpgcrCount = proposals.filter(p => p.status === 'Sent to FPGC-R').length;
    const pendingClaimsCount = claims.filter(c => c.status === 'Pending').length;

    return (
        <div className="p-6 space-y-8">

            {/* Header */}
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-3xl font-bold">HOD Dashboard</h1>
                    <p className="text-sm text-muted-foreground">
                        Department submission tracking
                    </p>
                </div>
                <Button>
                    View all submissions
                    <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Pending Review"
                    value={pendingCount}
                    description="Awaiting evaluator assignment"
                    icon={AlertCircle}
                    accent="bg-yellow-500"
                />
                <StatCard
                    title="Under Evaluation"
                    value={underReviewCount}
                    description="Currently with evaluators"
                    icon={ClipboardCheck}
                    accent="bg-blue-500"
                />
                <StatCard
                    title="Sent to FPGC-R"
                    value={sentToFpgcrCount}
                    description="Forwarded this month"
                    icon={Send}
                    accent="bg-purple-500"
                />
                <StatCard
                    title="Honorarium Claims"
                    value={pendingClaimsCount}
                    description="Awaiting decision"
                    icon={DollarSign}
                    accent="bg-green-500"
                />
            </div>

            {/* Recent Submissions */}
            <Card>
                <CardHeader>
                    <CardTitle className="text-base">Recent Submissions</CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-muted/50">
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Title</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Student</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {proposals.slice(0, 5).map((proposal) => (
                                    <tr
                                        key={proposal.id}
                                        className="hover:bg-muted/30 transition-colors"
                                    >
                                        <td className="px-4 py-3 text-sm font-medium max-w-xs truncate">
                                            {proposal.title}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-muted-foreground">
                                            {proposal.studentName}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">
                                            {proposal.submissionDate}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge
                                                variant="outline"
                                                className={statusStyles[proposal.status] ?? ''}
                                            >
                                                {proposal.status}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button variant="outline" size="sm">
                                                View
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            {/* Pending Claims */}
            <Card>
                <CardHeader>
                    <CardTitle className="text-base">Pending Honorarium Claims</CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-muted/50">
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Evaluator</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Proposal</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider hidden md:table-cell">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {claims.filter(c => c.status === 'Pending').slice(0, 5).map((claim) => (
                                    <tr
                                        key={claim.id}
                                        className="hover:bg-muted/30 transition-colors"
                                    >
                                        <td className="px-4 py-3 text-sm font-medium">
                                            {claim.evaluatorName}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell max-w-xs truncate">
                                            {claim.proposalTitle}
                                        </td>
                                        <td className="px-4 py-3 text-sm font-medium">
                                            N${claim.amount.toLocaleString()}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-muted-foreground hidden md:table-cell">
                                            {claim.date}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex gap-2">
                                                <Button size="sm" variant="outline"
                                                    className="border-green-500/30 text-green-400 hover:bg-green-500/20">
                                                    Approve
                                                </Button>
                                                <Button size="sm" variant="outline"
                                                    className="border-red-500/30 text-red-400 hover:bg-red-500/20">
                                                    Reject
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

        </div>
    );
}