<template>
    <div>
        <Head title="Отчеты по товарам" />
        <h1 class="mb-6 text-2xl font-semibold">
            Отчеты
            <span v-for="organization in organizations" :key="`title${organization.id}`">
                <span v-if="form.organization_id == organization.id"><span class="font-medium">/</span>{{ organization.name }}</span>
            </span>
        </h1>

        <ReportNavbar />

        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="relative w-full px-4 py-3 bg-white rounded appearance-none form-select-icon focus:shadow-outline">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
            </div>
            <div class="flex mt-2 md:mt-0">
                <a target="_blank" :href="`/reports/export-item?` + getUrlParams()" class="flex items-center justify-end p-2 mr-2 text-xs font-medium leading-5 text-white duration-200 bg-indigo-400 rounded-lg hover:bg-indigo-500 focus:outline-none">
                    <icon name="export" class="w-5 h-4" />
                </a>
                <button @click="form.modal = true" class="btn-gray">
                    <span>Фильтр/Поиск</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-10 px-4 py-3 border-r">#</th>
                    <th class="px-4 py-3 border-l border-r">Название Товара</th>
                    <th class="px-4 py-3 border-l border-r">Смета</th>
                    <th class="px-4 py-3 border-l border-r">Количество</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                </tr>
                <tr v-for="item in items" :key="item.id" :class="item.column == 'sum' ? 'bg-green-200' : 'duration-150 hover:bg-amber-50 focus:bg-amber-50'">
                    <td v-if="item.column === 'name'" class="border-t bg-sky-200" colspan="5">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ item.name }}
                        </div>
                    </td>
                    <td v-if="item.column === 'content'" colspan="1" class="border-t">
                        <div class="flex items-center px-4 py-1 font-semibold">
                            {{ item.index }}
                        </div>
                    </td>
                    <td v-if="item.column === 'content'" class="border-t border-l">
                        <Link :href="`/reports/items/${form.organization_id}/${item.id}/supplier` + getUrlDatas()" class="flex items-center px-4 py-1 font-medium hover:underline">
                            {{ item.name }}
                        </Link>
                    </td>
                    <td v-if="item.column === 'content'" class="border-t border-l">
                        <Link v-if="item.estimate" :href="`/reports/items/${form.organization_id}/${item.id}/supplier` + getUrlDatas()" class="flex items-center px-4 py-1 font-medium hover:underline">
                            {{ item.estimate }} 
                            {{ item.measurement }}
                        </Link>
                    </td>
                    <td v-if="item.column === 'content'" class="border-t border-l">
                        <Link :href="`/reports/items/${form.organization_id}/${item.id}/supplier` + getUrlDatas()" class="flex items-center px-4 py-1 font-medium hover:underline">
                            {{ item.count }}
                            {{ item.measurement }}
                        </Link>
                    </td>
                    <td v-if="item.column === 'content'" class="border-t border-l">
                        <Link :href="`/reports/items/${form.organization_id}/${item.id}/supplier` + getUrlDatas()" class="flex items-center px-4 font-semibold text-green-600 hover:underline whitespace-nowrap">
                            {{ item.sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                    <td v-if="item.column === 'sum'" class="px-4 py-2 font-semibold text-red-600 border-t" colspan="2">ИТОГО</td>
                    <td v-if="item.column === 'sum'" class="py-2 border-t border-l">
                        <div class="flex items-center px-4 font-semibold text-red-600 whitespace-nowrap">
                            {{ item.estimate_count?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td v-if="item.column === 'sum'" class="py-2 border-t border-l">
                        <div class="flex items-center px-4 font-semibold text-red-600 whitespace-nowrap">
                            {{ item.category_count?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td v-if="item.column === 'sum'" class="py-2 border-t border-l">
                        <div class="flex items-center px-4 font-semibold text-red-600 whitespace-nowrap">
                            {{ item.category_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="4">ВСЕГО</td>
                    <td class="border-t border-l" colspan="2">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_item?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td class="px-4 py-3 border-t" colspan="6">Не найдено.</td>
                </tr>
            </table>
        </div>
        <ModalLeft @serach="getData" @close="form.modal = !form.modal" :isOpen="form.modal">
            <ul role="list" class="-my-6">
                <li class="pb-4">
                    <select-input v-model="form.supplier_id" class="w-full" label="Поставщик">
                        <option :value="null"></option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                    </select-input>
                </li>
                <li class="pb-4">
                    <select-input v-model="form.item_category_id" class="w-full" label="Категория">
                        <option :value="null"></option>
                        <option v-for="item_category in item_categories" :key="item_category.id" :value="item_category.id">{{ item_category.name }}</option>
                    </select-input>
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата от:</label>
                        <date-picker v-model="form.begin" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата до:</label>
                        <date-picker v-model="form.end" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
            </ul>
        </ModalLeft>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Dropdown from '@/Shared/Dropdown'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'
import ModalLeft from '@/Shared/ModalLeft'
import SelectInput from '@/Shared/SelectInput'
import ReportNavbar from './ReportNavbar'
import { Calendar, DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'

export default {
    components: {
        Head,
        Icon,
        Dropdown,
        Link,
        Pagination,
        TextInput,
        LoadingButton,
        SelectInput,
        ModalLeft,
        DatePicker,
        ReportNavbar,
    },
    layout: Layout,
    props: {
        filters: Object,
        organizations: Object,
        items: Object,
        sum_item: Number,
        item_categories: Object,
        suppliers: Object,
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
                supplier_id: this.filters.supplier_id,
                modal: false,
                begin: this.filters.begin,
                end: this.filters.end,
                item_category_id: this.filters.item_category_id,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports/items', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        getData() {
            this.$inertia.get('/reports/items', pickBy(this.form), { preserveState: true })
            this.form.modal = false
        },
        getUrlParams() {
            let begin = ''
            let end = ''
            if (this.form.begin) {
                let d = new Date(this.form.begin)
                begin = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
            }
            if (this.form.end) {
                let d = new Date(this.form.end)
                end = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
            }

            return new URLSearchParams({ supplier_id: this.form.supplier_id, organization_id: this.form.organization_id, item_category_id: this.form.item_category_id, begin: begin, end: end }).toString()
        },
        getUrlDatas() {
            let begin = ''
            let end = ''
            let url = ''
            if (this.form.begin) {
                let d = new Date(this.form.begin)
                begin = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
                url += '?' + new URLSearchParams({ begin: begin }).toString()
            }
            if (this.form.end) {
                let d = new Date(this.form.end)
                end = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
                url += url == '' ? '?' + new URLSearchParams({ end: end }).toString() : '&' + new URLSearchParams({ end: end }).toString()
            }
            if (this.form.supplier_id){
                url += url == '' ? '?' + new URLSearchParams({ supplier_id: this.form.supplier_id }).toString() : '&' + new URLSearchParams({ supplier_id: this.form.supplier_id }).toString()
            }

            return url
        },
    },
}
</script>
