<script setup>
import DefaultAuthCard from '@/components/Auths/DefaultAuthCard.vue'
import InputGroup from '@/components/Auths/InputGroup.vue'
import { ref } from 'vue'
import { authStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'
const isLoading = ref(false)
const router = useRouter()
const flashOption = ref({})
const auth_store = authStore()

const phone = ref('')
const password = ref('')

const getFirstRouteWithPermission = () => {
  let route = '/dashboard'
  if (!auth_store.user.permissions) {
    return route
  }


  if (auth_store.user.permissions.filter(permission => permission.name == 'menu.dashboard').length) {
    return '/dashboard'
  }

  auth_store.user.permissions.forEach(permission => {
    if (permission.name == "menu.work_orders") {
      route = '/work_orders'
    }
  })

  return route
}

if (auth_store.user != null) {
  router.push(getFirstRouteWithPermission())
}

async function login() {
  isLoading.value = true;

  const credentials = {
    user_phone: phone.value,
    password: password.value,
  }
  await axios.post(`${auth_store.api}/login`, credentials).then(response => {
    auth_store.setSession(response.data);
    if (auth_store.user != null) {
      router.push(getFirstRouteWithPermission())
    }
  }).catch(error => {
    isLoading.value = false;
    if (typeof error.response.data !== undefined)
      flashOption.value = {
        open: true,
        title: "Atencion",
        message: error.response.data.message,
      }
  });
}
</script>

<template>
  <div class="my-36 mx-36">
    <DefaultAuthCard subtitle="Acceder al sistema" title="Ingrese Sus Credenciales">
      <InputGroup label="Nombre de usuario o teléfono" placeholder="Nombre de usuario o teléfono" v-model="phone">
        <fa icon="phone"></fa>
      </InputGroup>

      <InputGroup label="Contraseña" type="password" placeholder="Contraseña" v-model="password">
        <fa icon="lock"></fa>
      </InputGroup>

      <div class="mb-5 mt-6">
        <button @click="login"
          class="w-full cursor-pointer rounded-lg border border-primary bg-primary p-4 font-medium text-white transition hover:bg-opacity-90">Login</button>
      </div>
    </DefaultAuthCard>
  </div>
</template>
