<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';

interface Product {
  id: number;
  title: string;
  slug: string;
  price: number;
  available: boolean;
  updated_at: string;
}

interface Filters {
  page: number;
  perPage: number;
  sortBy: string | null;
  sortDir: 'asc' | 'desc';
}

const props = defineProps<{
  products: { data: Product[]; links: { url: string | null; label: string; active: boolean }[] };
  filters: Filters;
}>();
const { products, filters: initialFilters } = props;

const filters = ref<Filters>({ ...initialFilters });

// Перезагрузка списка с новыми параметрами
function reload(): void {
  router.get('/fake-shop', filters.value, { preserveState: true, replace: true });
}

// Переключение направления сортировки
function toggleSortDir(): void {
  filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
  reload();
}

// Хелперы форматирования
function formatPrice(value: number): string {
  return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'EUR' }).format(value);
}

function formatDate(value: string): string {
  return dayjs(value).format('DD.MM.YYYY HH:mm');
}
</script>

<template>
  <div class="p-6">
    <h1 class="mb-4 text-2xl font-bold">Фейковый магазин</h1>

    <!-- Фильтры: perPage и сортировка -->
    <div class="mb-4 flex items-center space-x-4">
      <div>
        <label class="mr-2">Показывать на странице:</label>
        <select v-model="filters.perPage" @change="reload" class="rounded border px-2 py-1">
          <option v-for="n in [5, 10, 15, 20, 50]" :key="n" :value="n">{{ n }}</option>
        </select>
      </div>
      <div>
        <label class="mr-2">Сортировать по:</label>
        <select v-model="filters.sortBy" @change="reload" class="rounded border px-2 py-1">
          <option value="">По умолчанию</option>
          <option value="updated_at">Обновлению</option>
          <option value="price">Цене</option>
          <option value="title">Названию</option>
        </select>
        <button @click="toggleSortDir" class="ml-2 rounded border px-2 py-1">
          {{ filters.sortDir === 'asc' ? '▲' : '▼' }}
        </button>
      </div>
    </div>

    <!-- Таблица продуктов -->
    <table class="min-w-full overflow-hidden rounded-lg shadow">
      <thead class="">
        <tr>
          <th class="px-4 py-2 text-left">Название</th>
          <th class="px-4 py-2 text-left">Ссылка</th>
          <th class="px-4 py-2 text-right">Цена</th>
          <th class="px-4 py-2 text-left">Доступность</th>
          <th class="px-4 py-2 text-left">Обновлено</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products.data" :key="product.id" class="border-t">
          <td class="px-4 py-2">{{ product.title }}</td>
          <td class="px-4 py-2">
            <Link :href="`/fake-shop/product/${product.slug}`" class="text-blue-500 hover:underline"> Перейти</Link>
          </td>
          <td class="px-4 py-2 text-right">{{ formatPrice(product.price) }}</td>
          <td class="px-4 py-2">
            <span :class="product.available ? 'text-green-600' : 'text-red-600'">
              {{ product.available ? 'В наличии' : 'Нет в наличии' }}
            </span>
          </td>
          <td class="px-4 py-2">{{ formatDate(product.updated_at) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Пагинация -->
    <div class="mt-4 flex justify-center space-x-2">
      <template v-for="link in products.links" :key="link.label">
        <Link
          v-if="link.url"
          :href="link.url"
          class="rounded border px-3 py-1"
          :class="link.active ? 'text-black' : 'text-blue-500'"
          v-html="link.label"
        >
        </Link>
        <span v-else class="rounded border bg-gray-200 px-3 py-1 text-gray-500" v-html="link.label"></span>
      </template>
    </div>
  </div>
</template>
