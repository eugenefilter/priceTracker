<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { History, Link2, ShoppingBasket, Store } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
  {
    title: 'Fake Shop',
    href: '/fake-shop',
    icon: Store,
  },
  {
    title: 'Links',
    href: '/links',
    icon: Link2,
  },
  {
    title: 'Products',
    href: '/products',
    icon: ShoppingBasket,
  },
  {
    title: 'Products change',
    href: '/products/change',
    icon: History,
  },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>
  </Sidebar>
  <slot />
</template>
