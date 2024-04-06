<script setup lang="ts">
import StatsSection from './StatsSection.vue'
import ChartThree from '@/components/Charts/ChartThree.vue'
import TableOne from '@/components/Tables/TableOne.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import LineChartComponent from '../../components/Charts/LineChartComponent.vue'
import { ref, reactive, onMounted } from 'vue'
import { authStore } from '@/stores/auth'
import CarouselComponent from '../../components/Ui/CarouselComponent.vue'
const auth_store = authStore()
const config = reactive({
  headers: { Authorization: auth_store.token }
})

const lineChartLabels = ref()
const lineChartSeries= ref()
const images = ref()

onMounted(async () => {
  getEventTotals()
  getEventImages()
})

const getEventTotals = async () => {
  let url = `${auth_store.api}/dashboard/events/totals?by=week`
    await axios.get(url, config).then(response => {
      lineChartSeries.value = [{
        name: 'Eventos',
        data: response.data.count
      }]
      lineChartLabels.value = response.data.labels
    }).catch(e => {
        console.log(e)
    })
}

const getEventImages = async () => {
  let url = `${auth_store.api}/dashboard/events/images?last=10`
    await axios.get(url, config).then(response => {
      images.value = response.data.images
    }).catch(e => {
        console.log(e)
    })
}
</script>

<template>
  <DefaultLayout>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
      <StatsSection />
    </div>

    <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
      <LineChartComponent :labels="lineChartLabels" :series="lineChartSeries"/>
      <CarouselComponent :images="images"/>
      <ChartThree />
      <div class="col-span-12 xl:col-span-8">
        <TableOne />
      </div>
    </div>
  </DefaultLayout>
</template>
