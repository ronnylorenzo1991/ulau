<script setup>
import { ref, computed } from 'vue'

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
    validation: {
      default: ''
    }
})

const localValue = ref(props.modelValue)

const getDynamicClass = computed(() => {
  return props.validation ? 'border-primary' : 'border-stroke'
})
</script>

<template>
  <div :class="customClasses">
    <label class="mb-2.5 block text-black dark:text-white">
      {{ label }}
      <span v-if="required" class="text-meta-1">*</span>
    </label>
    <input :type="type" :placeholder="placeholder" @change="changed" v-model="localValue"
      class="w-full rounded border-[1.5px] text-black bg-transparent py-3 px-5 font-normal outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:text-white dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
      :class="getDynamicClass" />
      <div class="absolute pb-6">
        <label class="text-[#ff309e] text-xs" v-text="validation"></label>
      </div>
    </div>
</template>
