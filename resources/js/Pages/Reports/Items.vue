<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>

        <ReportNavbar />

        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="form-select-icon relative px-4 py-3 w-full rounded focus:shadow-outline appearance-none">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
            </div>
            <div class="flex">
                <a target="_blank" :href="`/reports/export-item?` + getUrlParams()" class="flex items-center justify-end mr-2 px-2 py-2 text-white text-xs font-medium leading-5 bg-indigo-400 hover:bg-indigo-500 rounded-lg focus:outline-none duration-200">
                    <icon name="export" class="w-5 h-5" />
                </a>
                <button @click="form.modal = true" class="btn-gray mt-2 md:mt-0">
                    <span>Фильтр/Поиск</span>
                </button>
            </div>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-10 border-r">#</th>
                    <th class="px-4 py-3 border-l border-r">Название Товара</th>
                    <th class="px-4 py-3 border-l border-r">Количество</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                    <th class="px-4 py-3 border-l">Итого</th>
                </tr>
                <tr v-for="item in items" :key="item.id" class="hover:bg-amber-50 focus:bg-amber-50 duration-150">
                    <td class="border-t" :colspan="item.count ? 1 : 5" :class="!item.count ? 'bg-sky-200' : ''">
                        <div :class="'flex items-center px-4 py-1 ' + (item.count ? 'font-medium' : 'font-semibold')">
                            {{ item.count ? item.index : item.name }}
                        </div>
                    </td>
                    <td v-if="item.count" class="border-l border-t">
                        <Link :href="`/reports/items/${form.organization_id}/${item.item_id}/supplier` + getUrlDatas()" class="flex items-center px-4 py-1 hover:underline font-medium">
                            {{ item.name }}
                        </Link>
                    </td>
                    <td v-if="item.count" class="border-l border-t">
                        <Link :href="`/reports/items/${form.organization_id}/${item.item_id}/supplier` + getUrlDatas()" class="flex items-center px-4 py-1 hover:underline font-medium">
                            {{ item.count }}
                            {{ item.measurement }}
                        </Link>
                    </td>
                    <td v-if="item.count" class="border-l border-t">
                        <Link :href="`/reports/items/${form.organization_id}/${item.item_id}/supplier` + getUrlDatas()" class="flex items-center px-4 text-green-600 hover:underline whitespace-nowrap font-semibold">
                            {{ item.sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                    <td v-if="item.count" class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ item.category_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-l border-t" colspan="2">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ sum_item?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td class="px-4 py-3 border-t" colspan="5">Не найдено.</td>
                </tr>
            </table>
        </div>
        <ModalLeft @serach="getData" @close="form.modal = !form.modal" :isOpen="form.modal">
            <ul role="list" class="-my-6">
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
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
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

            return new URLSearchParams({ organization_id: this.form.organization_id, item_category_id: this.form.item_category_id, begin: begin, end: end }).toString()
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

            return url
        },
    },
}
</script>
