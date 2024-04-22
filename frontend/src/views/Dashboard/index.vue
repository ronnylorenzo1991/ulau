<script setup>
import StatsSection from './StatsSection.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import LineChartComponent from '../../components/Charts/LineChartComponent.vue'
import { ref, reactive, onMounted, nextTick } from 'vue'
import { authStore } from '@/stores/auth'
import CarouselComponent from '../../components/Ui/CarouselComponent.vue'
import DonutChartComponent from '../../components/Charts/DonutChartComponent.vue'
import TableThree from '../../components/Tables/TableThree.vue'
import TableComponent from '../../components/Tables/TableComponent.vue'
import ModalComponent from '../../components/Ui/ModalComponent.vue'
const auth_store = authStore()
const config = reactive({
  headers: { Authorization: auth_store.token }
})

const currentItem = ref([])
const events = ref([])
const links = ref()
const from = ref()
const to = ref()
const total = ref()
const next_page = ref()
const prev_page = ref()
const current_page = ref()
const eventColumns = ref([
  {
    key: 'date_at',
    text: 'fecha',
  },
  {
    key: 'anomalies',
    childrenKey: 'class_label',
    text: 'Anomalia',
    type: 'multiple'
  },
  {
    key: 'image_path',
    text: 'Imagen',
    type: 'image'
  }
])
const showImageModal = ref(false)
const lineChartLabels = ref()
const lineChartSeries = ref()
const donutChartSeries = ref()
const donutChartLabels = ref()
const images = ref()
const filters = ref({
  donutChartRange: 'day'
})

onMounted(async () => {
  getEventTotals()
  getEventImages()
  getAnomaliesByClass()
  getAllEvents()
})

const closeImageModal = () => {
  showImageModal.value = false
}

const openImageModal = (item) => {
  showImageModal.value = true
  currentItem.value = item
  nextTick(() => {
    showImageMap()
  })
}

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

const getAnomaliesByClass = async () => {
  let url = `${auth_store.api}/dashboard/anomalies/class?filterBy=${filters.value.donutChartRange}`
  await axios.get(url, config).then(response => {
    donutChartLabels.value = response.data.labels
    donutChartSeries.value = response.data.series
  }).catch(e => {
    console.log(e)
  })
}

const getAllEvents = async (link = null) => {
  let url = link ? `${link}&per_page=5` : `${auth_store.api}/events?per_page=5`
  await axios.get(url, config).then(response => {
    prepareTableData(response.data)
  }).catch(e => {
    console.log(e)
  })
}

const showImageMap = () => {
  let canvasObj = document.getElementById('image-map-canvas')
  const ctx = canvasObj.getContext('2d')
  canvasObj.width = 640
  canvasObj.height = 480

  let background = new Image()
  background.src = currentItem.value.image_path

  background.onload = () => {
    const strokeColors = {
      'Hematoma': '#FFA70B',
      'Melanosis': '#D34053',
      'Salmon Inv': '#219653',
      'Salmon': '#219653',
      'Cracking': '#219653',
      'Gapping': '#219653',
    }

    ctx.drawImage(background, 0, 0, 640, 480)
    setTimeout(() => {
      if (currentItem.value.anomalies) {
        currentItem.value.anomalies.forEach(anomaly => {
          const coordinates = eval(anomaly.coordinates)
          ctx.strokeStyle = strokeColors[anomaly.class_label]
          ctx.lineWidth = 3;
          ctx.strokeRect(coordinates[0], coordinates[1], coordinates[2] - coordinates[0], coordinates[3] - coordinates[1])
          ctx.stroke()
        })
      }
    })
  }
}

const prepareTableData = async (response) => {
  events.value = response.data.data
  links.value = response.data.links
  current_page.value = response.data.current_page
  from.value = response.data.from
  to.value = response.data.to
  total.value = response.data.total
  next_page.value = null
  prev_page.value = null
}
</script>

<template>
  <DefaultLayout>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6 xl:grid-cols-7 2xl:gap-7.5">
      <StatsSection />
    </div>

    <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
      <LineChartComponent :labels="lineChartLabels" :series="lineChartSeries" v-if="lineChartLabels" />
      <CarouselComponent :images="images" title="Últimas Capturas"/>
      <DonutChartComponent title="Resumen Anomalias" :labels="donutChartLabels" :series="donutChartSeries"
        v-if="donutChartLabels" />
      <TableComponent :items="events" :columns="eventColumns" @imageClicked="openImageModal" :links="links"
        @navigate="getAllEvents" :current_page="current_page" :prev_page="prev_page" :next_page="next_page" :from="from"
        :to="to" :total="total" />
    </div>
    <ModalComponent v-if="showImageModal" :show="showImageModal" :hiddenAcceptButton="true" @cancel="closeImageModal"
      size="7">
      <canvas id="image-map-canvas"></canvas>
      <!-- <img :src="currentItem.image_path"> -->
    </ModalComponent>
  </DefaultLayout>
</template>
