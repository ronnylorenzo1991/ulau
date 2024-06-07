<script setup>
import { ref, computed, nextTick } from 'vue'
const context = ref()

const props = defineProps({
    display: Boolean,
    quickEventFocused: {
        type: Boolean,
        default: false,
    },
})

const left = ref(0)
const top = ref(0)
const show = ref(false)

const style = computed(() => {
    return {
        top: top.value + 'px',
        left: left.value + 'px',
    }
})

const open = (evt) => {
    left.value = evt.pageX || evt.clientX;
    top.value = evt.clientY;

    nextTick(() => context.value.focus());
    show.value = true;
}

const close = () => {
    if (props.quickEventFocused) {
        return
    }
    show.value = false;
    left.value = 0;
    top.value = 0;
}

defineExpose({
    open,
    close
});
</script>

<template>
    <div class="context-menu bg-white dark:border-strokedark dark:bg-boxdark" v-show="show" :style="style" ref="context" tabindex="0" @blur="close">
        <slot></slot>
    </div>
</template>
<style scoped>
.context-menu {
    position: fixed;
    z-index: 50;
    outline: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    cursor: pointer;
}
</style>