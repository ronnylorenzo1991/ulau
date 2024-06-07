<script setup>
import { ref } from 'vue'
import DataStatsComponent from '@/components/DataStats/DataStatsComponent.vue'


const emit = defineEmits(['clicked'])
const props = defineProps({
  cardItems: {
    default: []
  },
  activeClasses: {
    devault: []
  }
})

const selected = ref()

const selectIt = (label) => {
  selected.value = selected.value == label ? null : label
  emit('clicked', selected.value)
}

</script>

<template>
  <!-- Card Item Start -->
  <DataStatsComponent 
    :title="item.title" :label="item.label" :total="item.total" :icon="item.icon" :percent="item.percent"
    v-for="(item, index) in cardItems" :key="index" />
  <!-- Card Item End -->
</template>
