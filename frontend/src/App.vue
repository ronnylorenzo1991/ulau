<script setup>
import { RouterView } from 'vue-router'
import { useRouter } from 'vue-router'
import { ref, computed } from "vue";
import { authStore } from '@/stores/auth'
import { provide } from 'vue'
import { getCurrentInstance } from 'vue';
const app = getCurrentInstance();
const $filters = app.appContext.config.globalProperties.$filters;

provide('can', $filters.can);
const router = useRouter()
const auth_store = authStore();
auth_store.setApi();
function profile() {
  router.push({
    name: 'profile'
  })
}

const getUserFullName = computed(() => {
   return auth_store.user.name
})

function logout() {
  auth_store.logout()
  router.push({
    name: 'login'
  })
}
</script>

<template>
  <RouterView />
</template>
