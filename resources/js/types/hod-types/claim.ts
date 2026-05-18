import type { EvaluatorType } from './evaluator';

export interface IClaim {
    id: string;
    evaluatorId?: string;
    evaluatorType?: EvaluatorType;
    evaluatorName: string;
    proposalTitle: string;
    date: string;
    amount: number;
    status: 'Pending' | 'Approved' | 'Rejected';
}
