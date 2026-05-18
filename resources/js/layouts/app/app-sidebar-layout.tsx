import { use, useEffect } from 'react';
import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { useSidebar } from '@/components/ui/sidebar';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { useSidebarState } from '@/hooks/use-sidebar-state';
import type { AppLayoutProps } from '@/types';

function SidebarPersistence() {
    const {open} = useSidebar();
    const {persist} = useSidebarState(false);

    useEffect(() => {
        persist(open);
    }, [open, persist]);

    return null;
}

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: AppLayoutProps) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
