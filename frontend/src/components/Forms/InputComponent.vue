<script setup>
import { ref } from 'vue'

const emit = defineEmits(['update:modelValue', 'change'])
const changed = ($event) => {
  emit('update:modelValue', localValue.value)
  emit('change', $event.target.value)
}

const props = defineProps({
  label: String,
    type: String,
    placeholder: String,
    customClasses: String,
    required: {
      type: Boolean,
      default: false
    },
    modelValue: {
      default: false,
    },
})

const localValue = ref(props.modelValue)
</script>

<template>
  <div :class="customClasses">
    <label class="mb-2.5 block text-black dark:text-white">
      {{ label }}
      <span v-if="required" class="text-meta-1">*</span>
    </label>
    <input :type="type" :placeholder="placeholder" @change="changed" v-model="localValue"
      class="w-full rounded border-[1.5px] text-black border-stroke bg-transparent py-3 px-5 font-normal outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:text-white dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
  </div>
</template>
