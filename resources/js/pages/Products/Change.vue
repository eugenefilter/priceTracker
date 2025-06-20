<script setup lang="ts">
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
  products: {
    data: Array<any>;
    products: Array<{
      product: { title: string };
      old_price: number;
      new_price: number;
      changed_at: string;
      availability: boolean;
    }>;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Product Change',
    href: '/products/change',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <h1 class="text-left text-2xl font-bold">История изменений</h1>
      <div v-if="products.data.length">
        <table class="w-full rounded border shadow">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="p-2">Товар</th>
              <th class="p-2">Старая цена</th>
              <th class="p-2">Новая цена</th>
              <th class="p-2">Дата обновления</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products.data" :key="product.id" class="border-t">
              <td class="p-2">{{ product.product.title }}</td>
              <td class="p-2">{{ product.old_price }}</td>
              <td class="p-2">{{ product.new_price }}</td>
              <td class="p-2">{{ new Date(product.changed_at).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>

        <Pagination :links="products.links" />
      </div>
      <div v-else class="text-gray-500">Список пуст</div>
    </div>
  </AppLayout>
</template>
