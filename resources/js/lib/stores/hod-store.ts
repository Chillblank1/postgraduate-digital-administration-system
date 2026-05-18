import { create } from 'zustand';
import type { IProposal } from '@/types/hod-types/proposal';
import type { IClaim } from '@/types/hod-types/claim';
import type { IEvaluator, IEvaluatorSummary } from '@/types/hod-types/evaluator';

type HODState = {
    proposals: IProposal[];
    claims: IClaim[];
    evaluators: IEvaluator[];
    selectedProposal: IProposal | null;
    statusFilter: string;
    typeFilter: string;

    setSelectedProposal: (proposal: IProposal | null) => void;
    setStatusFilter: (filter: string) => void;
    setTypeFilter: (filter: string) => void;
    assignEvaluator: (proposalId: string, evaluator: IEvaluatorSummary) => void;
    updateProposalStatus: (proposalId: string, status: string) => void;
    processClaim: (claimId: string, status: 'Approved' | 'Rejected') => void;
};

export const useHODStore = create<HODState>((set) => ({
    proposals: [
        {
            id: '1',
            studentName: 'Anna Shikongo',
            title: 'Machine Learning Applications in Rural Healthcare',
            supervisor: 'Dr. Hambira',
            type: 'PhD',
            evaluator: null,
            assignedEvaluator: null,
            status: 'Pending',
            submissionDate: '2026-05-10',
        },
        {
            id: '2',
            studentName: 'Josef Nakale',
            title: 'Renewable Energy Adoption in Namibian SMEs',
            supervisor: 'Prof. Amukoshi',
            type: 'MSc',
            evaluator: 'Dr. Nangolo',
            assignedEvaluator: {
                evaluatorId: '1',
                evaluatorType: 'internal',
                assignedAt: '2026-05-09',
            },
            status: 'Under Review',
            submissionDate: '2026-05-08',
        },
        {
            id: '3',
            studentName: 'Selma Nghifikwa',
            title: 'Water Resource Management in Northern Namibia',
            supervisor: 'Dr. Hamutwe',
            type: 'PhD',
            evaluator: 'Dr. Iipinge',
            assignedEvaluator: {
                evaluatorId: '2',
                evaluatorType: 'internal',
                assignedAt: '2026-04-29',
            },
            status: 'Approved',
            submissionDate: '2026-04-28',
        },
        {
            id: '4',
            studentName: 'Thomas Hamutenya',
            title: 'Digital Transformation in Public Sector',
            supervisor: 'Prof. Amukoshi',
            type: 'MSc',
            evaluator: 'Dr. Nangolo',
            assignedEvaluator: {
                evaluatorId: '1',
                evaluatorType: 'internal',
                assignedAt: '2026-04-16',
            },
            status: 'Sent to FPGC-R',
            submissionDate: '2026-04-15',
        },
        {
            id: '5',
            studentName: 'Maria Uushona',
            title: 'Cybersecurity Frameworks for Financial Institutions',
            supervisor: 'Dr. Hambira',
            type: 'PhD',
            evaluator: null,
            assignedEvaluator: null,
            status: 'Pending',
            submissionDate: '2026-05-12',
        },
    ],

    claims: [
        {
            id: '1',
            evaluatorId: '3',
            evaluatorType: 'external',
            evaluatorName: 'Prof. Haufiku',
            proposalTitle: 'Renewable Energy Adoption in Namibian SMEs',
            date: '2026-05-14',
            amount: 3500,
            status: 'Pending',
        },
        {
            id: '2',
            evaluatorId: '2',
            evaluatorType: 'internal',
            evaluatorName: 'Dr. Iipinge',
            proposalTitle: 'Water Resource Management in Northern Namibia',
            date: '2026-05-13',
            amount: 3500,
            status: 'Pending',
        },
        {
            id: '3',
            evaluatorId: '5',
            evaluatorType: 'external',
            evaluatorName: 'Prof. Shilongo',
            proposalTitle: 'Digital Transformation in Public Sector',
            date: '2026-04-20',
            amount: 4000,
            status: 'Approved',
        },
    ],

    evaluators: [],
    selectedProposal: null,
    statusFilter: 'all',
    typeFilter: 'all',

    setSelectedProposal: (proposal) => set({ selectedProposal: proposal }),
    setStatusFilter: (filter) => set({ statusFilter: filter }),
    setTypeFilter: (filter) => set({ typeFilter: filter }),

    assignEvaluator: (proposalId, evaluator) =>
        set((state) => ({
            proposals: state.proposals.map((p) =>
                p.id === proposalId
                    ? {
                          ...p,
                          evaluator: evaluator.name,
                          assignedEvaluator: {
                              evaluatorId: evaluator.id,
                              evaluatorType: evaluator.type,
                              assignedAt: new Date().toISOString().split('T')[0],
                          },
                          status: 'Under Review',
                      }
                    : p,
            ),
        })),

    updateProposalStatus: (proposalId, status) =>
        set((state) => ({
            proposals: state.proposals.map((p) =>
                p.id === proposalId ? { ...p, status } : p,
            ),
        })),

    processClaim: (claimId, status) =>
        set((state) => ({
            claims: state.claims.map((c) =>
                c.id === claimId ? { ...c, status } : c,
            ),
        })),
}));