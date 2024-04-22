<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { authStore } from '@/stores/auth'

import DataStatsComponent from '@/components/DataStats/DataStatsComponent.vue'
const auth_store = authStore()
const config = reactive({
    headers: { Authorization: auth_store.token }
})

const cardItems = ref([
  {
    icon: 'exclamation-triangle',
    label: 'Melanosis',
    title: 'Melanosis',
    total: 0,
    percent: 0
  },
  {
    icon: 'exclamation-circle',
    title: 'Hematoma',
    label: 'Hematoma',
    total: 0,
    percent: 0
  },
  {
    icon: 'drumstick-bite',
    title: 'Gapping',
    label: 'Gapping',
    total: 0,
    percent: 0
  },
  {
    icon: 'heart-broken',
    title: 'Cracking',
    label: 'Cracking',
    total: 0,
    percent: 0
  },
  {
    icon: 'fa-undo',
    title: 'Invertidos',
    label: 'Salmon Inv',
    total: 0,
    percent: 0
  },
  {
    icon: 'fa-bolt',
    title: 'Cicatriz',
    label: 'Cicatriz',
    total: 0,
    percent: 0
  },
  {
    icon: 'fish',
    title: 'Total',
    label: 'Salmon',
    total: 0,
    percent: 0
  }
])

onMounted(async () => {
  getAnomaliesByClass()
})

const getAnomaliesByClass = async () => {
  let url = `${auth_store.api}/dashboard/anomalies/class`
    await axios.get(url, config).then(response => {
      cardItems.value.map(cardItem => {
        let result = response.data.data.filter(item => item.label == cardItem.label)
        cardItem.total = result[0]?.quantity
      })
    }).catch(e => {
        console.log(e)
    })
}
</script>

<template>
  <!-- Card Item Start -->
    <DataStatsComponent :title="item.title" :label="item.label" :total="item.total" :icon="item.icon" :percent="item.percent" v-for="(item, index) in cardItems" :key="index"/>
  <!-- Card Item End -->
</template>
