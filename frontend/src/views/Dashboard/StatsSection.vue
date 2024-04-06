<script setup lang="ts">
import { ref } from 'vue'
import DataStatsComponent from '@/components/DataStats/DataStatsComponent.vue'

const cardItems = ref([
  {
    icon: 'eye',
    title: 'Melanosis',
    total: '3.456K',
    percent: 0.43
  },
  {
    icon: 'eye',
    title: 'Hematomas',
    total: '45,2K',
    percent: 4.35
  },
  {
    icon: 'eye',
    title: 'Gapping',
    total: '2.450',
    percent: 2.59
  },
  {
    icon: 'eye',
    title: 'Picados',
    total: '3.456',
    percent: -0.95
  }
])
</script>

<template>
  <!-- Card Item Start -->
    <DataStatsComponent :title="item.title" :total="item.total" :icon="item.icon" :percent="item.percent" v-for="(item, index) in cardItems" :key="index"/>
  <!-- Card Item End -->
</template>
