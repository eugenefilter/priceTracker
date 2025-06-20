<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowBigLeft, ArrowBigRight } from 'lucide-vue-next';

defineProps<{
  links: Array<{
    url: string | null
    label: string
    active: boolean
  }>
}>();
</script>

<template>
  <div class="mt-4 flex flex-wrap gap-2">
    <Link
      v-for="(page, i) in links"
      :key="i"
      :href="page.url ?? ''"
      class="flex items-center gap-1 px-3 py-1 rounded border text-sm"
      :class="{
        'bg-blue-500 text-white': page.active,
        'text-gray-700 hover:bg-gray-100': !page.active,
        'pointer-events-none text-gray-400': !page.url,
      }"
    >
      <template v-if="page.label.includes('Previous')">
        <ArrowBigLeft class="w-4 h-4" />
      </template>

      <template v-else-if="page.label.includes('Next')">
        <ArrowBigRight class="w-4 h-4" />
      </template>

      <template v-else>
        {{ page.label.replace(/<\/?[^>]+(>|$)/g, '') }}
      </template>
    </Link>
  </div>
</template>
