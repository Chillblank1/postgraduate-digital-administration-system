import { Link } from '@inertiajs/react';
import { BaggageClaim, BookOpen, FileText, FolderGit2, LayoutGrid, Mail, User } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

type Props ={
    authRole?: string;
}

//auth role implementation will be done once ready 
export function AppSidebar({authRole="HOD"}: Props) {
       const mainNavItems: NavItem[] = [
        ...(authRole === "HOD" ? [
            {
                title: 'Overview',
                // href:  '/hod/overview', (replace with actual controller later)
                href:'/', //replace with actual controller
                icon: LayoutGrid,
            },
            {
                title: 'Submissions',
                href:  '/hod/proposals', //replace with actual controller
                icon: FileText,
            },
              {
                title: 'Honorarium Claims',
                href: '/hod/claims', //replace with actual controller
                icon: BaggageClaim,
            },
            {
                title: 'Evaluators',
                href: '/hod/evaluators', //replace with actual controller
                icon: User,
            },
            {
                title: 'FPGC-R',
                href: '/hod/fpgc-r', //replace with actual controller
                icon: Mail,
            }
        ] : [
             {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        ]),
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/react-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#react',
            icon: BookOpen,
        },
    ];
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
