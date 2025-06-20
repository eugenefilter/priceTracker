<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  url: '',
});

const submit = () => {
  form.post(route('links.store'));
};

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
      <h1 class="mb-4 text-xl font-bold">Добавить товар</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="url" class="block text-sm font-medium">URL товара</label>
          <input v-model="form.url" type="url" id="url" class="w-full rounded border p-2" required />
          <div v-if="form.errors.url" class="text-sm text-red-500">{{ form.errors.url }}</div>
        </div>
        <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Добавить</button>
      </form>
    </div>
  </AppLayout>
</template>
