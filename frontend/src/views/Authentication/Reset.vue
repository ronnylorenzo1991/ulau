<template>
  <div class="text-center flex flex-col">
    <div>
      <img class="mx-auto w-48" src="/images/logo.png" alt="logo" />
      <p class="mb-2">Restablecer contraseña</p>
      <v-alert :options="flashOption"></v-alert>
    </div>

    <div class="text-center">
      <span
        v-show="countFlag"
        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-teal-700 bg-teal-100 last:mr-0 mr-1"
      >
        Ud será redireccionado en {{ countDown }}
      </span>
    </div>

    <div class="py-4">
      <v-input placeholder="Nueva contraseña" type="password" v-model="password" icon="lock" />
    </div>
    <div class="py-4">
      <v-input placeholder="Repetir contraseña" type="password" v-model="password_confirmation" icon="lock" />
    </div>

    <div class="text-center py-5">
      <v-button class="w-full" @click="reset" :load="load">Restablecer contraseña</v-button>
      <router-link to="/login" class="text-teal-700 text-sm bold m-3">¿Ya tienes cuenta?</router-link>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { authStore } from "../../stores/auth";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const countDown = ref(4); // 4 sec
const countFlag = ref(false);

//----------------
const flashOption = ref({})
const token = ref("");
const auth_store = authStore();
const router = useRouter();

const email = ref("");
const password = ref("");
const password_confirmation = ref("");

const load = ref(false);

onMounted(() => {
  const route = useRoute();
  token.value = route.params.token;
  email.value = route.query.email
});

const countDownTimer = async () => {
  countFlag.value = true;
  if (countDown.value > 0) {
    setTimeout(() => {
      countDown.value -= 1;
      countDownTimer();
    }, 1000);
  } else {
    countDown.value = 4;
    countFlag.value = false;
    router.push({ name: "login" });
  }
};
const reset = async () => {
  load.value = true;
  const data = {
    token: token.value,
    email: email.value,
    password: password.value,
    password_confirmation: password_confirmation.value,
  }
  await axios.post(`${auth_store.api}/reset`, data).then(response => {
      if (response.status == 200) {
        const data = response.data;
        flashOption.value = {
          open: true,
          color: "success",
          title: data.title,
          message: data.message,
        };
        countDownTimer();
      }
    }).catch(error => {
      const data = error.response.data;
      flashOption.value = {
        open: true,
        color: "error",
        title: data.title,
        message: data.message,
        errors: data.errors,
      };
    });

  load.value = false;
}
</script>

<style></style>
