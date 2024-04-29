<template>
    <component is="tr" :class="getRowColor"
               class="border-b">
        <template v-if="!isChildrenRow">
            <td @click="$emit('expandRow', index)">
                <div class="p-1 w-full mx-auto"
                     :class=" hasChildren() ? 'cursor-pointer' : null">
                    {{ (index + 1) + (currentPage * perPage) - perPage }}
                    <fa icon="caret-right" v-if="hasChildren() && !isExpanded(index)"></fa>
                    <fa icon="caret-down" v-if="hasChildren() && isExpanded(index)"></fa>
                </div>
            </td>
            <td v-for="header in columns" :key="header.id" class="p-2">
                <div v-if="helpers.nativeGet(header, 'key') === 'status'">
                    <span :class="getStatusClass(item, helpers.nativeGet(item, header.value))">{{ item.rca ? 'RCA' : helpers.nativeGet(item, header.value) }}</span>
                </div>
                <div v-else-if="helpers.nativeGet(header, 'key') === 'review_status'">
                    <span :class="getReviewStatusClass(helpers.nativeGet(item, header.value))">{{ item.rca ? helpers.nativeGet(item, 'rca_reason.name') : helpers.nativeGet(item, header.value) }}</span>
                </div>
                <div v-else-if="helpers.nativeGet(header, 'key') === 'active'">
                    <span>{{ item.active ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <span v-else-if="header?.type == 'date'">{{ helpers.formatDate(item[header.value]) }}</span>
                <span v-else>{{ helpers.nativeGet(item, header.value) }}</span>
            </td>
            <td v-if="withActions" class="w-[10%] text-center">
            </td>
        </template>
        <template v-else>
            <td colspan="99" v-if="hasChildren()" class="p-2" v-show="isExpanded(index)">
                <table class="w-full text-xs text-left">
                    <thead>
                    <tr class="border-2 bg-gray-200 py-3 px-6 w-[50%]">
                        <td v-for="header in childrenColumns" class="p-2">
                            {{ header.text }}
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(children, index) of helpers.nativeGet(item, childrens)" :key="index">
                        <td v-for="header in childrenColumns" :key="header.id" class="p-2">
                            <div v-if="helpers.nativeGet(header, 'key') === 'status'">
                                <span :class="getStatusClass(item, helpers.nativeGet(children, header.value))">{{ children.rca ? 'RCA' : helpers.nativeGet(children, header.value) }}</span>
                            </div>
                            <span v-else>{{ helpers.nativeGet(children, header.value) || "-" }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </template>
    </component>
</template>

<script setup>
import helpers from '../../libs/helpers'
import {computed} from 'vue'

const props = defineProps({
    item: {
        default: null,
    },
    index: {
        type: Number,
        default: 0
    },
    withActions: {
        type: Boolean,
        default: false,
    },
    isChildrenRow: {
        type: Boolean,
        default: false,
    },
    columns: {
        default: [],
    },
    childrenColumns: {
        default: [],
    },
    expandedRows: {
        default: [],
    },
    actions: {
        default: [],
    },
    childrens: {
        default: ''
    },
    currentPage: {
        default: 1
    },
    perPage: {
        default: 15
    },
})

const hasChildren = () => {
    return helpers.nativeGet(props.item, props.childrens, []).length > 0
}

const getStatusClass = (item, status) => {
    switch (status) {
        case 'completado':
            return 'rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-green-600/20'
        case 'incompleto':
            if(item.jobs.every(item => item.status.id === 1) && !item.rca) {
                return 'rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-blue-700/10'
            }
            return 'rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10'
        case 'pendiente':
            return 'rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-blue-700/10'
        case 'aprobado':
            return 'rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-blue-700/10'
        case 'reparado':
          return 'rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-blue-700/10'
    }
}

const getReviewStatusClass = (status) => {
    switch (status) {
        case 'aprobado':
            return 'rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20'
        case 'pendiente a revisión':
            return 'rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/10'
        case 'desaprobado':
            return 'rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10'
    }
}
const isExpanded = (index) => {
    return props.expandedRows.includes(index)
}

const getRowColor = computed(() => {
    if (props.isChildrenRow) {
        return 'bg-gray-50'
    }

   return props.index % 2 == 0  ? 'bg-gray-50 hover:bg-teal-100' : 'bg-teal-100 hover:bg-teal-100'
})
</script>

<style scoped>

</style>
