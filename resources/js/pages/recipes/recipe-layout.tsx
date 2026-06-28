import type { PropsWithChildren } from 'react';

import SidebarLayout from '@/layouts/sidebar-layout';

import type { NavItem } from '@/types';
import { create } from '@/routes/recipes';

const navItems: NavItem[] = [
    {
        title: 'Create Recipe',
        href: create(),
        icon: null,
        exact: true
    }
];

export default function RecipeLayout({
    children,
}: PropsWithChildren) {
    return (
        <SidebarLayout
            title="Recipes"
            description="Manage your recipes"
            navItems={navItems}
        >
            {children}
        </SidebarLayout>
    );
}