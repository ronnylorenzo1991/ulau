<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import BreadcrumbDefault from '@/components/Breadcrumbs/BreadcrumbDefault.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '../../components/Tables/TableComponent.vue'
import { authStore } from '@/stores/auth'
import SingleSelect from '@/components/Forms/SelectGroup/SingleSelectComponent.vue'
import InputGroup from '@/components/Forms/InputComponent.vue'
import Multiselect from '@vueform/multiselect'
import '@/assets/css/multiselect.css'
import Modal from '@/components/Modal/Modal.vue'

const auth_store = authStore()
const config = reactive({
  headers: { Authorization: auth_store.token }
})
const isLoading = ref(false)
const pageTitle = ref('Usuarios')
const newUser = ref({
  id: null,
  name: null,
  phone: null,
  roles: [],
})
const lists = ref({})
const showModal = ref(false)
const validationErrors = ref([])
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
    key: 'phone',
    text: 'telefono',
  },
  {
    key: 'actions',
  }
])
const tableActions = ref({
  edit: true,
  show: false,
  delete: true,
  downloadItem: false,
})

onMounted(async () => {
  navigate()
  loadLists(['roles'])
})

const navigate = async (link = null) => {
  let url = link ? `${link}&per_page=5` : `${auth_store.api}/users?per_page=5`
  await axios.get(url, config).then(response => {
    prepareTableData(response.data)
  }).catch(e => {
    console.log(e)
  })
}

const resetValidationErrors = () => {
  validationErrors.value = []
}

const resetData = () => {
  resetValidationErrors()
  newUser.value = {
    id: null,
    name: null,
    phone: null,
    roles: [],
  }
}

const closeModal = () => {
  resetData()
  showModal.value = false
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

const getValidationText = (key) => {
  return validationErrors.value[key] ? validationErrors.value[key].join(', ') : ''
}

const openModal = () => {
  showModal.value = true
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

const edit = (item) => {
  prepareEditData(item)
  showModal.value = true
}

const prepareEditData = (item) => {
  newUser.value.id = item.id
  newUser.value.name = item.name
  newUser.value.phone = item.phone
  item.roles.forEach(role => { newUser.value.roles.push(role.id) })
}

const save = async () => {
  isLoading.value = true
  if (newUser.value.id) {
    const url = `${auth_store.api}/users/${newUser.value.id}`
    axios.put(url, newUser.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
        navigate()
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
    const url = `${auth_store.api}/users`
    axios.post(url, newUser.value, config)
      .then(response => {
        isLoading.value = false
        dialog.success(response.data.message)
        closeModal()
        navigate()
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

const remove = async (item) => {
  dialog.confirm('Segura que desea eliminar este elemento?')
    .then((confirm) => {
      if (confirm) {
        if (item.id) {
          axios.delete(`${auth_store.api}/users/${item.id}`, config)
            .then(response => {
              dialog.success(response.data.message)
              navigate()
            }).catch(({ response }) => {
              dialog.error(response.data.message)
              console.log('error', response.data)
              return
            })
        }
      }
      navigate()
    })
}

</script>

<template>
  <DefaultLayout>
    <div class="mx-auto max-w-full">
      <!-- Breadcrumb Start -->
      <BreadcrumbDefault :pageTitle="pageTitle" />
      <!-- Breadcrumb End -->
      <TableComponent :items="settings" :columns="settingsColumn" :links="links" @navigate="navigate" @delete="remove"
        :actions="tableActions" @edit="edit" :current_page="current_page" :prev_page="prev_page" :next_page="next_page"
        :from="from" :to="to" :total="total">
        <template v-slot:table-menu>
          <button @click="openModal"
            class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm border-teal-600 hover:border-teal-800 sm:mt-0 sm:w-auto bg-primary hover:bg-opacity-90">
            + Agregar Nuevo
          </button>
        </template>
      </TableComponent>
      <Modal v-if="showModal" @cancel="closeModal" @accept="save" title="Agregar Usuario" :show="showModal">
        <Multiselect v-model="newUser.roles" placeholder="Seleccione los Roles" :close-on-select="true" 
          :searchable="true" :options="lists.roles" mode="tags" />
        <div class="absolute pb-6">
          <label class="text-[#ff309e] text-xs" v-text="getValidationText('roles')"></label>
        </div>
        <InputGroup label="Nombre" type="text" placeholder="Nombre" v-model="newUser.name" class="my-5" :validation="getValidationText('name')" />
        <InputGroup label="Teléfono" type="text" placeholder="Teléfono" v-model="newUser.phone" class="my-5" :validation="getValidationText('phone')"/>
      </Modal>
    </div>
  </DefaultLayout>
</template>
