<script setup lang="ts">
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
  links: {
    data: Array<any>;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Links',
    href: '/links',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <h1 class="text-left text-2xl font-bold">Мои ссылки</h1>
      <Link :href="route('links.create')" class="w-52 cursor-pointer rounded bg-blue-500 px-4 py-2 text-center text-white hover:bg-blue-600">
        Добавить ссылку
      </Link>
      <div v-if="links.data.length">
        <table class="w-full rounded border shadow">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="p-2">URL</th>
              <th class="p-2">Создан</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="link in links.data" :key="link.id" class="border-t">
              <td class="p-2">{{ link.url }}</td>
              <td class="p-2">{{ new Date(link.created_at).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>

        <Pagination :links="links.links" />
      </div>
      <div v-else class="text-gray-500">Список пуст</div>
    </div>
  </AppLayout>
</template>
