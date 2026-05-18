/* Backend team, feel free to modify this file as you deem appropriate. */
import type {
    CreateExternalEvaluatorInput,
    CreateInternalEvaluatorInput,
    IExternalEvaluator,
    IInternalEvaluator,
    UpdateExternalEvaluatorInput,
    UpdateInternalEvaluatorInput,
} from '@/types/hod-types/evaluator';

export type EvaluatorListQuery = {
    search?: string;
    status?: string;
    page?: number;
    perPage?: number;
};

export type PaginatedResponse<T> = {
    data: T[];
    currentPage?: number;
    lastPage?: number;
    perPage?: number;
    total?: number;
};

const INTERNAL_EVALUATORS_ENDPOINT = '/api/internal-evaluators';
const EXTERNAL_EVALUATORS_ENDPOINT = '/api/external-evaluators';

const getCsrfToken = (): string | undefined =>
    document
        .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? undefined;

const buildUrl = (
    endpoint: string,
    query?: EvaluatorListQuery,
): string => {
    const params = new URLSearchParams();

    if (query?.search) {
        params.set('search', query.search);
    }

    if (query?.status) {
        params.set('status', query.status);
    }

    if (query?.page !== undefined) {
        params.set('page', query.page.toString());
    }

    if (query?.perPage !== undefined) {
        params.set('per_page', query.perPage.toString());
    }

    const queryString = params.toString();

    return queryString ? `${endpoint}?${queryString}` : endpoint;
};

const request = async <T>(
    endpoint: string,
    options: RequestInit = {},
): Promise<T> => {
    const response = await fetch(endpoint, {
        credentials: 'same-origin',
        ...options,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(getCsrfToken() ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}),
            ...options.headers,
        },
    });

    if (!response.ok) {
        throw new Error(`Evaluator request failed with ${response.status}`);
    }

    if (response.status === 204) {
        return undefined as T;
    }

    return (await response.json()) as T;
};

export const listInternalEvaluators = (
    query?: EvaluatorListQuery,
): Promise<PaginatedResponse<IInternalEvaluator> | IInternalEvaluator[]> =>
    request(buildUrl(INTERNAL_EVALUATORS_ENDPOINT, query));

export const getInternalEvaluator = (
    id: string,
): Promise<IInternalEvaluator> =>
    request(`${INTERNAL_EVALUATORS_ENDPOINT}/${id}`);

export const createInternalEvaluator = (
    evaluator: CreateInternalEvaluatorInput,
): Promise<IInternalEvaluator> =>
    request(INTERNAL_EVALUATORS_ENDPOINT, {
        method: 'POST',
        body: JSON.stringify(evaluator),
    });

export const updateInternalEvaluator = (
    id: string,
    evaluator: UpdateInternalEvaluatorInput,
): Promise<IInternalEvaluator> =>
    request(`${INTERNAL_EVALUATORS_ENDPOINT}/${id}`, {
        method: 'PATCH',
        body: JSON.stringify(evaluator),
    });

export const deleteInternalEvaluator = (id: string): Promise<void> =>
    request(`${INTERNAL_EVALUATORS_ENDPOINT}/${id}`, {
        method: 'DELETE',
    });

export const listExternalEvaluators = (
    query?: EvaluatorListQuery,
): Promise<PaginatedResponse<IExternalEvaluator> | IExternalEvaluator[]> =>
    request(buildUrl(EXTERNAL_EVALUATORS_ENDPOINT, query));

export const getExternalEvaluator = (
    id: string,
): Promise<IExternalEvaluator> =>
    request(`${EXTERNAL_EVALUATORS_ENDPOINT}/${id}`);

export const createExternalEvaluator = (
    evaluator: CreateExternalEvaluatorInput,
): Promise<IExternalEvaluator> =>
    request(EXTERNAL_EVALUATORS_ENDPOINT, {
        method: 'POST',
        body: JSON.stringify(evaluator),
    });

export const updateExternalEvaluator = (
    id: string,
    evaluator: UpdateExternalEvaluatorInput,
): Promise<IExternalEvaluator> =>
    request(`${EXTERNAL_EVALUATORS_ENDPOINT}/${id}`, {
        method: 'PATCH',
        body: JSON.stringify(evaluator),
    });

export const deleteExternalEvaluator = (id: string): Promise<void> =>
    request(`${EXTERNAL_EVALUATORS_ENDPOINT}/${id}`, {
        method: 'DELETE',
    });
