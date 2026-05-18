export interface IFacultyPostgradRep {
    id: string;
    name: string;
    email: string;
    faculty: string;
    department: string;
    status: 'Active' | 'Inactive';
}