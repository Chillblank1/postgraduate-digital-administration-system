export type EvaluatorType = 'internal' | 'external';

export type EvaluatorStatus = 'Active' | 'Inactive' | 'Pending';

export interface IEvaluatorBase {
    id: string;
    type: EvaluatorType;
    title?: string;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    specialization?: string;
    status: EvaluatorStatus;
    createdAt?: string;
    updatedAt?: string;
}

export interface IInternalEvaluator extends IEvaluatorBase {
    type: 'internal';
    staffNumber?: string;
    departmentId?: string;
    departmentName?: string;
    position?: string;
}

export interface IExternalEvaluator extends IEvaluatorBase {
    type: 'external';
    institution: string;
    country?: string;
    postalAddress?: string;
}

export type IEvaluator = IInternalEvaluator | IExternalEvaluator;

export interface IEvaluatorSummary {
    id: string;
    type: EvaluatorType;
    name: string;
    email: string;
    affiliation?: string;
}

export interface IEvaluatorAssignment {
    evaluatorId: string;
    evaluatorType: EvaluatorType;
    assignedAt?: string;
}

export type CreateInternalEvaluatorInput = Omit<
    IInternalEvaluator,
    'id' | 'type' | 'createdAt' | 'updatedAt'
>;

export type UpdateInternalEvaluatorInput =
    Partial<CreateInternalEvaluatorInput>;

export type CreateExternalEvaluatorInput = Omit<
    IExternalEvaluator,
    'id' | 'type' | 'createdAt' | 'updatedAt'
>;

export type UpdateExternalEvaluatorInput =
    Partial<CreateExternalEvaluatorInput>;
