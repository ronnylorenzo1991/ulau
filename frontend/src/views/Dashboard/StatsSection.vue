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
    icon: 'eye',
    label: 'Melanosis',
    title: 'Melanosis',
    total: 0,
    percent: 0
  },
  {
    icon: 'eye',
    title: 'Hematoma',
    label: 'Hematoma',
    total: 0,
    percent: 0
  },
  {
    icon: 'eye',
    title: 'Gapping',
    label: 'Gapping',
    total: 0,
    percent: 0
  },
  {
    icon: 'eye',
    title: 'Filete Invertido',
    label: 'Salmon inv',
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
    <DataStatsComponent :title="item.title" :total="item.total" :icon="item.icon" :percent="item.percent" v-for="(item, index) in cardItems" :key="index"/>
  <!-- Card Item End -->
</template>
