<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, ClipboardList, Settings, Table2 } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import type { Auth } from '@/types/auth';

const page = usePage<{ auth: Auth }>();
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

const mainNavItems: NavItem[] = [
    {
        title: 'Резервирования',
        href: '/reservations',
        icon: CalendarDays,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Столы',
        href: '/admin/tables',
        icon: Table2,
    },
    {
        title: 'Резервирования',
        href: '/admin/reservations',
        icon: CalendarDays,
    },
    {
        title: 'Заявки',
        href: '/admin/requests',
        icon: ClipboardList,
    },
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/reservations">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <template v-if="isAdmin">
                <SidebarSeparator />
                <SidebarGroup class="px-2 py-0">
                    <SidebarGroupLabel>
                        <Settings class="mr-1 h-3.5 w-3.5" />
                        Администрирование
                    </SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in adminNavItems" :key="item.title">
                            <SidebarMenuButton as-child :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
