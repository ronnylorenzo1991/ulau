<script setup lang="ts">
import { ref } from 'vue'
const props = defineProps({
  icon: {
    default: "user",
  },
  title: {
    default: "title",
  },
  total: {
    default: 0,
  },
  percent: {
    default: 0,
  },
})
</script>

<template>
  <!-- Card Item Start -->
  <div
    class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="flex">
      <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
        <fa :icon="props.icon" />
      </div>
      <div class="py-2 px-6 text-black text-2xl dark:text-white">
        {{ props.title }}
      </div>
    </div>

    <div class="mt-4 flex items-end justify-between">
      <div>
        <h4 class="text-title-md font-bold text-black dark:text-white">{{ props.total }}</h4>
      </div>

      <span class="flex items-center gap-1 text-sm font-medium"
        :class="{ 'text-meta-3': props.percent > 0, 'text-meta-5': props.percent < 0 }">
        {{ props.percent }}%
        <svg v-if="props.percent > 0" class="fill-meta-3" width="10" height="11" viewBox="0 0 10 11" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M4.35716 2.47737L0.908974 5.82987L5.0443e-07 4.94612L5 0.0848689L10 4.94612L9.09103 5.82987L5.64284 2.47737L5.64284 10.0849L4.35716 10.0849L4.35716 2.47737Z"
            fill="" />
        </svg>

        <svg v-if="props.percent < 0" class="fill-meta-5" width="10" height="11" viewBox="0 0 10 11" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M5.64284 7.69237L9.09102 4.33987L10 5.22362L5 10.0849L-8.98488e-07 5.22362L0.908973 4.33987L4.35716 7.69237L4.35716 0.0848701L5.64284 0.0848704L5.64284 7.69237Z"
            fill="" />
        </svg>
      </span>
    </div>
  </div>
  <!-- Card Item End -->
</template>
