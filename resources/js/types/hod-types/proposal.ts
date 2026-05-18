import type { IEvaluatorAssignment } from './evaluator';

export interface IProposal {
    id: string;
    studentName: string;
    title: string;
    supervisor: string;
    type: string;
    evaluator: string | null;
    assignedEvaluator?: IEvaluatorAssignment | null;
    status: string;
    submissionDate?: string;
}

