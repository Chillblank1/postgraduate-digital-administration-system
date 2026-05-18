import { usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';


const getSidebarDefault = (serverValue: boolean): boolean => {
    try {
        const stored = localStorage.getItem('sidebar-open');
        return stored !== null ? stored === 'true' : serverValue;
    } catch {
        return serverValue;
    }
};

type Props = {
    children: ReactNode;
    variant?: AppVariant;
};

export function AppShell({ children, variant = 'sidebar' }: Props) {
    const isOpen = usePage().props.sidebarOpen as boolean;

    if (variant === 'header') {
        return (
            <div className="flex min-h-screen w-full flex-col">{children}</div>
        );
    }

    return (<SidebarProvider defaultOpen={getSidebarDefault(isOpen)}>
                {children}
            </SidebarProvider>
    );
}
