<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, LayoutGrid, ShoppingCart, Users, BookMarked, Clock } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

// Mendapatkan data user dari Inertia
const page = usePage();
// @ts-expect-error: Ignoring type check for usePage props
const userRole = computed(() => page.props.auth?.user?.role);

// Menu untuk administrasi
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Manajemen User',
        href: '/admin/user-management',
        icon: Users,
    },
    {
        title: 'Manajemen Buku',
        href: '/admin/books',
        icon: BookOpen,
    },
    {
        title: 'Peminjaman',
        href: '/admin/loans',
        icon: BookMarked,
    },
    {
        title: 'Permintaan Baru',
        href: '/admin/loans/pending',
        icon: Clock,
    },
    {
        title: 'Buku Dipinjam',
        href: '/admin/loans/active',
        icon: ShoppingCart,
    },
];

// Menu untuk guru dan murid
const userNavItems: NavItem[] = [
    {
        title: 'Keranjang',
        href: '/cart',
        icon: ShoppingCart,
    },
];

// Menentukan menu yang akan ditampilkan berdasarkan role
const mainNavItems = computed<NavItem[]>(() => {
    if (userRole.value === 'administrasi') {
        return adminNavItems;
    } else {
        return userNavItems;
    }
});

const footerNavItems: NavItem[] = [
    {
        title: 'Perpustakaan Digital',
        href: '/',
        icon: BookOpen,
    }
];

// Menentukan route home berdasarkan role
const homeRoute = computed<string>(() => {
    if (userRole.value === 'administrasi') {
        return '/admin/dashboard';
    } else {
        return '/';
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeRoute">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
