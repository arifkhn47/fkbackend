import type { PropsWithChildren } from 'react';

import SidebarLayout from '@/layouts/sidebar-layout';

import type { NavItem } from '@/types';
import { create, index } from '@/routes/foods';

const navItems: NavItem[] = [
    {
        title: 'Foods',
        href: index(),
        icon: null,
        exact: true
    },
    {
        title: 'Add Food',
        href: create(),
        icon: null,
        exact: true
    }
];

export default function FoodLayout({
    children,
}: PropsWithChildren) {
    return (
        <SidebarLayout
            title="Foods"
            description="Manage your foods"
            navItems={navItems}
        >
            {children}
        </SidebarLayout>
    );
}