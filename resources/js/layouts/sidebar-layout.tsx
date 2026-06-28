import { Link } from '@inertiajs/react';
import type { PropsWithChildren, ReactNode } from 'react';

import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

interface SidebarLayoutProps extends PropsWithChildren {
    title: string;
    description?: string;
    navItems: NavItem[];
    sidebarHeader?: ReactNode;
}

export default function SidebarLayout({
    title,
    description,
    navItems,
    sidebarHeader,
    children,
}: SidebarLayoutProps) {
    const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="px-4 py-6">
            <Heading
                title={title}
                description={description}
            />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    {sidebarHeader && (
                        <div className="mb-4">
                            {sidebarHeader}
                        </div>
                    )}

                    <nav
                        className="flex flex-col space-y-1"
                        aria-label={title}
                    >
                        {navItems.map((item, index) => (
                            <Button
                                key={`${toUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn(
                                    'w-full justify-start',
                                    {
                                        'bg-muted':
                                            item.exact
                                                ? isCurrentUrl(item.href)
                                                : isCurrentOrParentUrl(item.href),
                                    }
                                )}
                            >
                                <Link href={item.href}>
                                    {item.icon && (
                                        <item.icon className="mr-2 h-4 w-4" />
                                    )}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}