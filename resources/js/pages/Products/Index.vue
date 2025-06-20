<script setup lang="ts">
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { User } from '@/types';
import { WebSocketService } from '@/websocket/client';
import { Link } from '@inertiajs/vue3';
import { inject, onBeforeUnmount, onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: '/products',
  },
];

interface Product {
  id: number;
  title: string;
  availability: string;
  price: number;
}

interface Link {
  url: string | null;
  label: string;
  active: boolean;
}

const props = defineProps<{
  products: {
    data: Product[];
    links: Link[];
  };
}>();

const productsList = ref<Product[]>(Array.isArray(props.products?.data) ? [...props.products.data] : []);
const user = inject<User>('user');

if (!user) {
  throw new Error('User must be provided via app.provide("user", user)');
}

let wsService: WebSocketService;

onMounted(() => {
  wsService = new WebSocketService('127.0.0.1', 8443, user.id, ['products']);
  wsService.connect();

  wsService.on('*', (_data, msg) => {
    console.log('[WS ANY]', msg.event, 'on', msg.channel, '=>', msg.data);

    const product = msg.data;
    const idx = productsList.value.findIndex((p) => p.id === product.id);
    if (idx === -1) {
      productsList.value.push(product);
    } else {
      productsList.value[idx] = product;
    }
  });
});

onBeforeUnmount(() => {
  wsService.close();
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <h1 class="mb-4 text-2xl font-bold">Список товаров</h1>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="product in productsList" :key="product.id" class="rounded-lg border p-4 shadow-md">
          <template v-if="product.id !== undefined">
            <h3 class="text-lg font-semibold">{{ product.title }}</h3>
            <p>ID: {{ product.id }}</p>
            <p>Title: {{ product.title }}</p>
            <p>
              Статус:
              <span class="mt-2">
                <button v-if="product.availability" class="mr-2 rounded bg-green-500 px-2 py-1 text-xs text-white">Доступен</button>
                <button v-if="!product.availability" class="mr-2 rounded bg-red-500 px-2 py-1 text-xs text-white">Нет в наличии</button>
              </span>
            </p>
            <p>Цена: {{ product.price }}</p>

            <div class="mt-10">
              <Link :href="`/product/check/${product.id}`" class="rounded bg-amber-800 px-2 py-1 text-amber-50"> Проверить </Link>
            </div>
          </template>
        </div>
      </div>

      <Pagination :links="products.links" />
    </div>
  </AppLayout>
</template>
