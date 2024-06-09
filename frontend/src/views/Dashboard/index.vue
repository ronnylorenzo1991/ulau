<script setup lang="ts">
import { ref, reactive, onMounted,computed } from 'vue'
import BreadcrumbDefault from '@/components/Breadcrumbs/BreadcrumbDefault.vue'
import CalendarCard from '@/components/CalendarCard.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import { Calendar } from '@fullcalendar/core'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import momentPlugin from '@fullcalendar/moment'
import Modal from '@/components/Modal/Modal.vue'
import InputGroup from '@/components/Forms/InputComponent.vue'
import SingleSelect from '@/components/Forms/SelectGroup/SingleSelectComponent.vue'
import { authStore } from '@/stores/auth'
import axios from 'axios'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/css/index.css';
import dayjs from 'dayjs'
import ContextMenu from "./ContextMenu.vue";
import Swal from 'sweetalert2'
import StatsSection from './StatsSection.vue'
import LineChartComponent from '../../components/Charts/LineChartComponent.vue'

const auth_store = authStore()
const config = reactive({
  headers: { authorization: auth_store.token }
})
const contextMenuKey = ref(0)
const contextMenuRef = ref(null)
const isQuickEventFocused = ref(false)
const calendarApi = ref(null)
const fullCalendarRef = ref(null)
const isLoading = ref(false)
const pageTitle = ref('Administración')
const showEventModal = ref(false)
const validationErrors = ref([])
const lineChartLabels = ref()
const lineChartSeries = ref()
const openTab = ref(1)

const statsCardItems = ref([
  {
    icon: 'dollar',
    title: 'Ganancias Mes',
    label: 'Cracking',
    total: 0,
    percent: 0
  },
  {
    icon: 'money-bill',
    title: 'Gastos Mes',
    label: 'Gapping',
    total: 0,
    percent: 0
  },
  {
    icon: 'exclamation-triangle',
    label: 'Melanosis',
    title: 'Prom Ganancia diaria',
    total: 0,
    percent: 0
  },
  {
    icon: 'exclamation-circle',
    label: 'Hematoma',
    title: 'Prom Inv. diaria',
    total: 0,
    percent: 0
  },
])

const turns = ref(['08:00', '11:00', '13:00'])

const newTurn = ref({
  date_at: null,
  time_at: null,
  client_id: -1,
  payment: 0,
  status_id: 1,
  observations: null,
})

const newBill = ref({
  date_at: null,
  payment: 0,
  product_name: null,
  description: null,
})

const lists = ref({})

const save = (eventType = null) => {
  if (!eventType) {
    openTab.value == 1 ? saveTurn() : saveBill()
  } else {
    eventType == 1 ? saveTurn() : saveBill()
  }
}

const saveBill = async () => {
  isLoading.value = true
  newBill.value.date_at = formatDate(newBill.value.date_at)
  if (newBill.value.id) {
    const url = `${auth_store.api}/bills/${newBill.value.id}`
    axios.put(url, newBill.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
      }).catch(({ response }) => {
        isLoading.value = false
        if (response.status === 422) {
          validationErrors.value = response.data.errors
        } else {
          console.log('error', response)
          dialog.error(response.data.message)
        }
      })
  } else {
    const url = `${auth_store.api}/bills`
    axios.post(url, newBill.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
      }).catch(({ response }) => {
        isLoading.value = false
        if (response.status === 422) {
          validationErrors.value = response.data.errors
        } else {
          console.log('error', response)
          dialog.error(response.data.message)
        }
      })
  }
}

const saveTurn = async () => {
  isLoading.value = true
  if (newTurn.value.id) {
    const url = `${auth_store.api}/turns/${newTurn.value.id}`
    newTurn.value.date_at = formatDate(newTurn.value.date_at)
    axios.put(url, newTurn.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
      }).catch(({ response }) => {
        isLoading.value = false
        if (response.status === 422) {
          validationErrors.value = response.data.errors
        } else {
          console.log('error', response)
          dialog.error(response.data.message)
        }
      })
  } else {
    const url = `${auth_store.api}/turns`
    newTurn.value.date_at = formatDate(newTurn.value.date_at)
    axios.post(url, newTurn.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
      }).catch(({ response }) => {
        isLoading.value = false
        if (response.status === 422) {
          validationErrors.value = response.data.errors
        } else {
          console.log('error', response)
          dialog.error(response.data.message)
        }
      })
  }
}


const loadLists = async (list) => {
  let url = `${auth_store.api}/lists?lists=${JSON.stringify(list)}`
  axios.get(url, config)
    .then(response => {
      if (response.status === 200) {
        lists.value = response.data.lists
      } else {
        dialog.error(response.data.message)
      }
    }).catch(error => {
      if (!error.response) {
        dialog.error()
      } else {
        dialog.error()
      }
    })
}

const handleDateSelect = (data) => {
  resetBillData()
  resetTurnData()
  showEventModal.value = true
  newTurn.value.date_at = data.date
  newBill.value.date_at = data.date
}

const resetTurnData = () => {
  newTurn.value = {
    date_at: null,
    time_at: null,
    client_id: -1,
    payment: 0,
    status_id: 1,
    observations: null,
  }
}

const resetBillData = () => {
  newBill.value = {
    date_at: null,
    payment: 0,
    product_name: null,
    description: null,
  }
}

const closeModal = () => {
  showEventModal.value = false
  refreshCalendarEvents()
  getEventTotals()
  resetTurnData()
  resetBillData()
  openTab.value = 1
}

const refreshCalendarEvents = () => {
  calendarApi.value.refetchEvents()
}

const eventClick = (item) => {
  prepareEditData(item)
  contextMenuRef.value.open(item.jsEvent)
}

const eventDrop = (item) => {
  if (item.event.extendedProps.hasOwnProperty('time_at')) {
    newTurn.value.date_at = dayjs(item.event.start).format('MM/DD/YYYY')
    newTurn.value.time_at = item.event.extendedProps.time_at
    newTurn.value.client_id = item.event.extendedProps.client_id
    newTurn.value.observations = item.event.extendedProps.observations
    newTurn.value.payment = item.event.extendedProps.payment
    newTurn.value.status_id = item.event.extendedProps.status_id
    newTurn.value.id = item.event.extendedProps.turn_id
    resetBillData()
    save(1)
    return
  }

  if (item.event.extendedProps.hasOwnProperty('product_name')) {
    newBill.value.date_at = dayjs(item.event.start).format('MM/DD/YYYY')
    newBill.value.product_name = item.event.extendedProps.product_name
    newBill.value.description = item.event.extendedProps.description
    newBill.value.payment = item.event.extendedProps.payment
    newBill.value.status_id = item.event.extendedProps.status_id
    newBill.value.id = item.event.extendedProps.bill_id
    resetTurnData()
    save(2)
    return
  }
}

onMounted(async () => {
  await loadLists(['clients'])
  await getStatsCardData()
  calendarApi.value = fullCalendarRef.value.getApi()
  getEventTotals()
})

const prepareEditData = (item) => {
  if (item.event.extendedProps.hasOwnProperty('time_at')) {
    newTurn.value.date_at = dayjs(item.event.start).format('MM/DD/YYYY')
    newTurn.value.time_at = item.event.extendedProps.time_at
    newTurn.value.client_id = item.event.extendedProps.client_id
    newTurn.value.observations = item.event.extendedProps.observations
    newTurn.value.payment = item.event.extendedProps.payment
    newTurn.value.status_id = item.event.extendedProps.status_id
    newTurn.value.id = item.event.extendedProps.turn_id
    resetBillData()
    return
  }

  if (item.event.extendedProps.hasOwnProperty('product_name')) {
    newBill.value.date_at = dayjs(item.event.start).format('MM/DD/YYYY')
    newBill.value.product_name = item.event.extendedProps.product_name
    newBill.value.description = item.event.extendedProps.description
    newBill.value.payment = item.event.extendedProps.payment
    newBill.value.status_id = item.event.extendedProps.status_id
    newBill.value.id = item.event.extendedProps.bill_id
    resetTurnData()
    return
  }
}

const getValidationText = (key) => {
  return validationErrors.value[key] ? validationErrors.value[key].join(', ') : ''
}

const formatDate = (date, format = 'YYYY-MM-DD') => {
  return dayjs(date).format(format)
}

const calendarOptions = ref({
  contentHeight: 1000,
  headerToolbar: {
    left: 'prevYear,prev,next,nextYear',
    center: 'title',
    right: 'today,dayGridMonth,listWeek,listDay'
  },
  events: `${auth_store.api}/dashboard/events`,
  plugins: [
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    listPlugin,
    momentPlugin,
  ],
  firstDay: 1,
  eventDisplay: 'auto',
  buttonText: {
    listMonth: 'month',
    listYear: 'year',
    listWeek: 'week',
    listDay: 'day'
  },
  initialView: 'dayGridMonth',
  eventOrder: 'eventTypeSort',
  editable: true,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true,
  weekNumbers: false,
  eventClick: eventClick,
  dateClick: handleDateSelect,
  eventDrop: eventDrop,
})

const keepContextMenu = () => {
  isQuickEventFocused.value = true
}

const unkeepContextMenu = () => {
  isQuickEventFocused.value = false
}

const editEvent = () => {
  openTab.value = newTurn.value.id ? 1 : 2
  showEventModal.value = true
  forceCloseContextMenu()
}

const cancelEvent = () => {
  forceCloseContextMenu()
  dialog.confirm('Segura que desea cancelar este turno?')
    .then((confirm) => {
      if (confirm) {
        if (newTurn.value.id) {
          axios.put(`${auth_store.api}/turns/cancel/${newTurn.value.id}`, newTurn.value, config)
            .then(response => {
              dialog.success(response.data.message)
              refreshCalendarEvents()
              resetTurnData()
            }).catch(({ response }) => {
              dialog.error(response.data.message)
              console.log('error', response.data)
              return
            })
        }
      }
      resetTurnData()
    })
}

const completeEvent = async () => {
  forceCloseContextMenu()
  let inputValue = 0
  const { value: payment } = await Swal.fire({
    title: "Completar turno",
    input: "text",
    inputLabel: "Inserta valor final del pago",
    inputValue,
    showCancelButton: true,
    inputValidator: (value) => {
      if (!value) {
        return "Tienes que agregar un valor al pago";
      }
    }
  });
  if (payment) {
    newTurn.value.payment = payment
    axios.put(`${auth_store.api}/turns/complete/${newTurn.value.id}`, newTurn.value, config)
      .then(response => {
        dialog.success(response.data.message)
        refreshCalendarEvents()
        resetTurnData()
      }).catch(({ response }) => {
        dialog.error(response.data.message)
        console.log('error', response.data)
        return
      })
  }
}

const deleteEvent = () => {
  forceCloseContextMenu()
  dialog.confirm('Segura que desea eliminar este turno?')
    .then((confirm) => {
      if (confirm) {
        if (newTurn.value.id) {
          axios.delete(`${auth_store.api}/turns/${newTurn.value.id}`, config)
            .then(response => {
              dialog.success(response.data.message)
              refreshCalendarEvents()
              resetTurnData()
            }).catch(({ response }) => {
              dialog.error(response.data.message)
              console.log('error', response.data)
              return
            })
        }
      }
      resetTurnData()
    })
}

const forceCloseContextMenu = () => {
  contextMenuKey.value++
}

const isTurnEvent = computed(() => {
  return newTurn.value.id != null
})

const getEventTotals = async () => {
  let url = `${auth_store.api}/dashboard/events/totals?by=week`
  await axios.get(url, config).then(response => {
    lineChartSeries.value = [
      {
        name: 'Ganancia',
        data: response.data.countTurns,
        color: '#ff5ce4'
      },
      {
        name: 'Inversión',
        data: response.data.countBills,
        color: '#f21862'
      },
    ]
    lineChartLabels.value = response.data.labels
  }).catch(e => {
    console.log(e)
  })
}

const getStatsCardData = async () => {
  let url = `${auth_store.api}/dashboard/stats`
  await axios.get(url, config).then(response => {
    statsCardItems.value[0].total = response.data.profit
    statsCardItems.value[1].total = response.data.expensesTotal
  }).catch(e => {
    console.log(e)
  })
}


</script>

<template>
  <DefaultLayout>
    <div class="mx-auto max-w-7xl">
      <loading :active="isLoading" v-if="isLoading" :is-full-page="true"></loading>
      <!-- Breadcrumb Start -->
      <BreadcrumbDefault :pageTitle="pageTitle" />
      <!-- Breadcrumb End -->
      <div class="grid grid-cols-1 gap-5 md:gap-6 2xl:gap-7.5 sm:grid-cols-4 py-10">
        <StatsSection :card-items="statsCardItems" />
      </div>
      <div class="py-5">
        <LineChartComponent :labels="lineChartLabels" :series="lineChartSeries" v-if="lineChartLabels" />
      </div>

      <div class="py-5">
        <FullCalendar :options='calendarOptions' ref="fullCalendarRef" />
      </div>
      <ContextMenu ref="contextMenuRef" :quick-event-focused="isQuickEventFocused" :key="contextMenuKey">
        <div class="p-2">
          <span @mouseover="keepContextMenu" @mouseout="unkeepContextMenu">
            <ul>
              <li class="p-2 hover:opacity-100 opacity-50" @click="editEvent">
                <a href="#" class="text-black dark:text-white select-none">
                  <fa icon="edit" class="pr-3" /> Editar
                </a>
              </li>
              <li class="p-2 hover:opacity-100 opacity-50" @click="completeEvent" v-show="isTurnEvent">
                <a href="#" class="text-black dark:text-white select-none">
                  <fa icon="check" class="pr-3" /> Completar
                </a>
              </li>
              <li class="p-2 hover:opacity-100 opacity-50" @click="cancelEvent" v-show="isTurnEvent">
                <a href="#" class="text-black dark:text-white select-none">
                  <fa icon="cancel" class="pr-3" /> Cancelar
                </a>
              </li>
              <li class="p-2 hover:opacity-100 opacity-50" @click="deleteEvent">
                <a href="#" class="text-black dark:text-white select-none">
                  <fa icon="trash" class="pr-3" /> Eliminar
                </a>
              </li>
            </ul>
          </span>
        </div>
      </ContextMenu>
      <Modal v-if="showEventModal" @cancel="closeModal" @accept="save" title="Agregar Turno" :show="showEventModal">
        <div>
          <div v-show="!newTurn.id && !newBill.id"
            class="mb-4 flex space-x-4 p-2 bg-white rounded-lg shadow-md bg-white dark:border-strokedark dark:bg-boxdark">
            <button :class="{ 'bg-primary text-white': openTab === 1 }"
              class="flex-1 py-2 px-4 rounded-md focus:outline-none focus:shadow-outline-pink transition-all duration-300"
              @click="openTab = 1">Turno</button>
            <button :class="{ 'bg-primary text-white': openTab === 2 }"
              class="flex-1 py-2 px-4 rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300"
              @click="openTab = 2">Inversión</button>
          </div>
          <div class="transition-all duration-300" v-if="openTab === 1">
            <label class="mb-3 block text-sm font-medium text-black dark:text-white">Hora</label>
            <div class="flex gap-2 my-2">
              <div class="flex">
                <input type="radio" name="radio-1" class="radio" v-model="newTurn.time_at" :value="turns[0]" />
                <label class="pl-2">1 Turno</label>
              </div>
              <div class="flex">
                <input type="radio" name="radio-1" class="radio" v-model="newTurn.time_at" :value="turns[1]" />
                <label class="pl-2">2 Turno</label>
              </div>
              <div class="flex">
                <input type="radio" name="radio-1" class="radio" v-model="newTurn.time_at" :value="turns[2]" />
                <label class="pl-2">3 Turno</label>
              </div>
            </div>
            <input type="time" v-model="newTurn.time_at"
              class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-normal outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
            <div class="absolute pb-6">
              <label class="text-red-500 text-xs" v-text="getValidationText('time')"></label>
            </div>
            <SingleSelect class="my-5" label="Clienta" placeholder="Seleccione la clienta" :options="lists.clients"
              v-model="newTurn.client_id"></SingleSelect>
            <InputGroup label="Observaciones" type="text" placeholder="Observaciones" v-model="newTurn.observations" />
          </div>
          <div class="transition-all duration-300" v-if="openTab === 2">
            <InputGroup label="Producto" type="text" placeholder="Producto" v-model="newBill.product_name"
              class="py-3" />
            <InputGroup label="Descripción" type="text" placeholder="Descripcion" v-model="newBill.description"
              class="py-3" />
            <InputGroup label="Costo" type="text" placeholder="Costo" v-model="newBill.payment" class="py-3" />
          </div>
        </div>
      </Modal>
    </div>
  </DefaultLayout>
</template>
<style>
input {
  color-scheme: dark;
}
</style>