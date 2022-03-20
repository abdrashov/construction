<template>
    <div>
        <Head title="Общий отчет" />
        <h1 class="mb-6 text-2xl font-semibold">
            Отчеты
            <span v-for="organization in organizations" :key="`title${organization.id}`">
                <span v-if="form.organization_id == organization.id"><span class="font-medium">/</span>{{ organization.name }}</span>
            </span>
        </h1>

        <ReportNavbar/>

        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="relative w-full px-4 py-3  bg-white rounded appearance-none form-select-icon focus:shadow-outline">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
            </div>
                <button @click="form.modal = true" class="btn-gray mt-2 md:mt-0">
                    <span>Фильтр/Поиск</span>
                </button>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 border-r">Накладные</th>
                    <th class="px-4 py-3 border-l border-r">Расходы</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                </tr>
                <tr v-if="form.organization_id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-4 font-semibold whitespace-nowrap">
                            {{ invoice_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-4 font-semibold whitespace-nowrap">
                            {{ expense_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-4 font-semibold whitespace-nowrap">
                            {{ (invoice_sum + expense_sum)?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-else>
                    <td class="px-4 py-3 border-t" colspan="3">Не найдено.</td>
                </tr>
            </table>
        </div>
        <ModalLeft @serach="getData" @close="form.modal = !form.modal" :isOpen="form.modal">
            <ul role="list" class="-my-6">
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
        invoice_sum: Number,
        expense_sum: Number,
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
                modal: false,
                begin: this.filters.begin,
                end: this.filters.end,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports/common', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form.begin = null
            this.form.end = null
            this.form.organization_id = null
            this.$inertia.get('/reports/common', pickBy(), { preserveState: true })
        },
        getData() {
            this.$inertia.get('/reports/common', pickBy(this.form), { preserveState: true })
            this.form.modal = false
        }
    },
}
</script>
