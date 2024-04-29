<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import BreadcrumbDefault from '@/components/Breadcrumbs/BreadcrumbDefault.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '../../components/Tables/TableComponent.vue'
import { authStore } from '@/stores/auth'
const auth_store = authStore()
const config = reactive({
  headers: { Authorization: auth_store.token }
})
const pageTitle = ref('Opciones Generales')
const settings = ref([])
const links = ref()
const from = ref()
const to = ref()
const total = ref()
const next_page = ref()
const prev_page = ref()
const current_page = ref()
const settingsColumn = ref([
  {
    key: 'name',
    text: 'nombre',
  },
  {
    key: 'description',
    text: 'descripción',
  },
  {
    key: 'value',
    text: 'valor',
  },
  {
    key: 'actions',
  }
])

onMounted(async () => {
  navigate()
})

const navigate = async (link = null) => {
  let url = link ? `${link}&per_page=5` : `${auth_store.api}/general_settings?per_page=5`
  await axios.get(url, config).then(response => {
    prepareTableData(response.data)
  }).catch(e => {
    console.log(e)
  })
}

const prepareTableData = async (response) => {
  settings.value = response.data.data
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
    <div class="mx-auto max-w-full">
      <!-- Breadcrumb Start -->
      <BreadcrumbDefault :pageTitle="pageTitle" />
      <!-- Breadcrumb End -->
      <TableComponent :items="settings" :columns="settingsColumn" :links="links"
      @navigate="navigate" :current_page="current_page" :prev_page="prev_page" :next_page="next_page" :from="from"
      :to="to" :total="total" />
    </div>
  </DefaultLayout>
</template>
