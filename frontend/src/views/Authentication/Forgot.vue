<template>
  <div>
    <div class="text-center flex flex-col">
      <div>
        <img class="mx-auto w-48" src="/images/logo.png" alt="logo" />
        <h4 class="mb-2">¿Olvidó su contraseña?</h4>
        <p class="text-sm mb-2">Ingrese su correo electrónico</p>
        <v-alert :options="flashOption"></v-alert>
      </div>
      <div class="py-4">
        <v-input v-model="email" placeholder="Correo electrónico" icon="user"></v-input>
      </div>
      <div class="text-center py-5">
        <v-button icon="lock" class="w-full" @click="send" :load="load">Enviar</v-button>
        <router-link to="/login" class="text-teal-700 text-sm bold m-3">Volver a inicio de sesión</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { authStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'
const flashOption = ref({})
const version = ref("0.0.1")

//----------------------------
const auth_store = authStore();
const router = useRouter();
const email = ref("");
const load = ref(false);

async function send () {
  const credentials = {
    email: email.value,
  }
  load.value = true;
  await axios.post(`${auth_store.api}/forgot`, credentials).then(res => {
    if (res.status == 200) {
      let data = res.data;
      flashOption.value = {
        open: true,
        color: "success",
        title: data.title,
        message: data.message,
        errors: {},
      };
    }
  }).catch(error => {
    const data = error.response.data;
    flashOption.value = {
      open: true,
      color: "error",
      title: "Atención",
      message: data.message,
    }
  });
  load.value = false;
}
</script>

<style></style>
