<template>
    <div>
        <div class="relative shadow-lg">
            <div class="flex flex-row py-2">
                <div class="flex ml-auto">
                    <slot name="table-menu">
                    </slot>
                    <button v-if="hasFilters"
                            class="uppercase bg-gray-100 border-gray-100 text-sm border-4 text-teal-600 py-1 px-2 rounded mx-2"
                            type="button" @click="toggleOpenFilters">
                        filtros
                        <fa icon="list" class="px-2"/>
                    </button>
                </div>
            </div>
            <div v-if="showFilters" class="z-20">
                <slot name="filters"></slot>
                <div class="flex flex-row">
                    <div class="mx-auto py-4">
                        <button @click="emit('clearFilters')"
                                class="bg-gray-100 hover:bg-gray-200 border-gray-100 hover:border-gray-200 text-sm border-4 text-gray-800 py-1 px-2 mx-2"
                                type="button"> Limpiar
                        </button>
                        <button @click="applyFilters"
                                class="bg-teal-600 hover:bg-teal-800 border-teal-600 hover:border-teal-800 text-sm border-4 text-white py-1 px-2"
                                type="button"> Aplicar
                        </button>
                    </div>
                </div>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="text-white">
                <tr class="border-2 bg-teal-600 py-3 px-6 w-[50%]" scope="col">
                    <th></th>
                    <th v-for="header in columns" :key="header.id" class="p-2">
                        <a href="#" @click.prevent="changeSort(header)" class="flex flex-row"
                           v-if="header.sortable && !isTableEmpty">
                            <p>
                                {{ header.text }}
                            </p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-5 h-5" v-if="sortBy !== header.value">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-5 h-5" v-if="sortBy === header.value && !sortAsc">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 17.25L12 21m0 0l-3.75-3.75M12 21V3"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-5 h-5" v-if="sortBy === header.value && sortAsc">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8.25 6.75L12 3m0 0l3.75 3.75M12 3v18"/>
                            </svg>
                        </a>
                        <div v-else>
                            {{ header.text }}
                        </div>
                    </th>
                    <th class="w-[80px] text-center" v-if="withActions">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center py-4" :colspan="99" v-if="isTableEmpty">No hay datos para
                        mostrar
                    </td>
                </tr>
                <template v-for="(item, index) of items" :key="index">
                    <DataTableRow :item="item"
                                  :index="index"
                                  :columns="columns"
                                  :children-columns="childrenColumns"
                                  :childrens="childrens"
                                  :with-actions="withActions"
                                  :actions="actions"
                                  :current-page="current_page"
                                  :is-children-row="false" :expanded-rows="expandedRows"
                                  @expandRow="expandRow"
                                  @edit="(value) => $emit('edit', value)"
                                  @complete="(value) => $emit('complete', value)"
                                  @show_images="(value) => $emit('show_images', value)"
                                  @show="(value) => $emit('show', value)"
                                  @print_preview="(value) => $emit('print_preview', value)"
                                  @review="(value) => $emit('review', value)"
                                  @remove="(value) => $emit('remove', value)">
                        <template v-slot:actions-cell>
                            <slot name="actionsMenu" v-bind:item="item"/>
                        </template>
                    </DataTableRow>
                    <DataTableRow :item="item"
                                  :index="index"
                                  :columns="columns"
                                  :children-columns="childrenColumns"
                                  :childrens="childrens"
                                  :with-actions="false"
                                  :is-children-row="true"
                                  :actions="childrenActions"
                                  :expanded-rows="expandedRows"></DataTableRow>
                </template>
                </tbody>
            </table>
        </div>
        <div class="py-5 flex flex-row" v-if="items.length">
            <div class="w-1/2 px-2 text-md text-gray-500">
                {{ from }} a {{ to }} de {{ total }}
            </div>
            <div class="w-1/2">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px float-right"
                     aria-label="Pagination">
                    <button v-for="link in links" aria-current="page" :disabled="!link.url"
                            @click="$emit('navigate', concatFilters(`${link.url}&sortBy=${sortBy}&sortDir=${sortAsc ? 'asc' : 'desc'}&exclude_completed=${excludeCompleted}&only_pendants=${onlyPendants}`))"
                            :class="`${ link.active ? 'z-10 bg-teal-600  text-white' : 'bg-white text-gray-500 hover:bg-gray-50'} relative inline-flex items-center px-4 py-2 border text-sm font-medium`">

                        <span v-if="link.label.includes('Anterior')">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                 fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>

                        <svg v-if="link.label.includes('Siguiente')" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>

                        <span v-if="!link.label.includes('Siguiente') && !link.label.includes('Anterior')">{{
                                link.label
                            }}</span>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>


<script setup>
import {ref, reactive, computed, watch} from 'vue'
import {defineComponent} from 'vue'
import DataTableRow from './DataTableRow.vue'
import { authStore } from '@/stores/auth'
import dayjs from 'dayjs'

const auth_store = authStore()
const config = reactive({
    headers: { authorization: auth_store.token }
})

const props = defineProps({
    withActions: {
        type: Boolean,
        default: true,
    },
    columns: Array,
    filters: {
        default: {}
    },
    items: {},
    excludeCompleted: false,
    onlyPendants: false,
    paginate: Boolean,
    store: Object,
    links: Array,
    current_page: Number,
    current_url: Object,
    prev_page: String,
    next_page: String,
    from: Number,
    to: Number,
    total: Number,
    childrenColumns: {
        default: []
    },
    actions: {
        default: [
            {
                event: 'edit',
                text: 'Editar',
                icon: 'edit',
                color: 'yellow',
            },
            {
                event: 'remove',
                text: 'Eliminar',
                icon: 'trash',
                color: 'red',
            }
        ]
    },
    childrenActions: {
        default: []
    },
    childrens: String,
})

const datatable = ref(null)
const sortAsc = ref(false)
const sortBy = ref('id')
const openModal = ref(false)
const model = ref({})
const itemsData = ref({})
const expandedRows = ref([])
const emit = defineEmits(['navigate'])
const showFilters = ref(false)

watch(() => props.excludeCompleted, () => {
    applyFilters()
})

watch(() => props.onlyPendants, () => {
    applyFilters()
})

const hasFilters = computed(() => {
    return Object.keys(props.filters).length > 0
})
const isTableEmpty = computed(() => {
    return props.items.length === 0
})

const closeModal = () => {
    openModal.value = !openModal.value
}

const applyFilters = () => {
    let link = `${getActiveLinkUrl()}&sortBy=${sortBy.value}&sortDir=${sortAsc.value ? 'asc' : 'desc'}&exclude_completed=${props.excludeCompleted}&only_pendants=${props.onlyPendants}`

    link = concatFilters(link)

    emit('navigate', link)
}

const concatFilters = (link) => {
    Object.keys(props.filters).forEach(item => {
        if (props.filters[item] !== null) {
            link += `&${item}=${props.filters[item] || ''}`
        }
    })

    return link
}

const toggleOpenFilters = () => {
    showFilters.value = !showFilters.value
}

const expandRow = (index) => {
    if (expandedRows.value.includes(index)) {
        expandedRows.value = expandedRows.value.filter(item => item !== index)
    } else {
        expandedRows.value.push(index)
    }
}

function changeSort(header) {
    if (sortBy.value === header.value) {
        sortAsc.value = !sortAsc.value
    } else {
        sortBy.value = header.value
        sortAsc.value = true
    }
    const link = `${getActiveLinkUrl()}&sortBy=${sortBy.value}&sortDir=${sortAsc.value ? 'asc' : 'desc'}&exclude_completed=${props.excludeCompleted}&only_pendants=${props.onlyPendants}`
    emit('navigate', link)
}

const getActiveLinkUrl = () => {
    const link = props.links.filter(item => item.active)

    return link[0].url
}

Object.assign(itemsData.value, props.items)
</script>
