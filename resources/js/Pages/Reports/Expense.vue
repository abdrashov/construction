<template>
    <div>
        <Head title="Отчеты о расходах" />
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
                    <option value="general">Общий</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
            </div>
            <div class="flex mt-2 md:mt-0">
                <a target="_blank" :href="`/reports/export-expense?` + getUrlParams()" class="flex items-center justify-end p-2 mr-2 text-xs font-medium leading-5 text-white duration-200 bg-indigo-400 rounded-lg hover:bg-indigo-500 focus:outline-none">
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
                    <th class="px-4 py-3 border-l border-r">Названия</th>
                    <th class="px-4 py-3 border-l border-r">Заметка</th>
                    <th class="px-4 py-3 border-l border-r">Категория</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                    <th class="px-4 py-3 border-l">Дата</th>
                </tr>
                <tr v-for="(expense, index) in expense_histories" :key="expense.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 font-semibold">
                            {{ index+1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div  class="flex items-center px-4 py-1 font-medium">
                            {{ expense.name }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div  class="flex items-center px-4 py-1 font-medium">
                            {{ expense.note }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div  class="flex items-center px-4 py-1 font-medium">
                            {{ expense.category }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ expense.price?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div  class="flex items-center px-4 py-1 font-medium whitespace-nowrap">
                            {{ expense.date }}
                        </div>
                    </td>
                </tr>
                <tr v-if="expense_histories.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-t border-l" colspan="3">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_expense?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="expense_histories.length === 0">
                    <td class="px-4 py-3 border-t" colspan="6">Не найдено.</td>
                </tr>
            </table>
        </div>
        <ModalLeft @serach="getData" @close="form.modal = !form.modal" :isOpen="form.modal">
            <ul role="list" class="-my-6">
                <li class="pb-4">
                    <select-input v-model="form.expense_category_id" class="w-full" label="Категория">
                        <option :value="null"></option>
                        <option v-for="expense_category in expense_categories" :key="expense_category.id" :value="expense_category.id">{{ expense_category.name }}</option>
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
        expense_histories: Object,
        sum_expense: Number,
        expense_categories: Object,
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
                modal: false,
                begin: this.filters.begin,
                end: this.filters.end,
                expense_category_id: this.filters.expense_category_id,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports/expense', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        getData() {
            this.$inertia.get('/reports/expense', pickBy(this.form), { preserveState: true })
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

            return new URLSearchParams({ expense_category_id: this.form.expense_category_id, organization_id: this.form.organization_id, begin: begin, end: end }).toString()
        },
    },
}
</script>
