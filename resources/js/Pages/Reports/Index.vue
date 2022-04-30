<template>
    <div>
        <Head title="Отчеты по поставщикам" />
        <h1 class="mb-6 text-2xl font-semibold">
            Отчеты
            <span v-for="organization in organizations" :key="`title${organization.id}`">
                <span v-if="form.organization_id == organization.id"><span class="font-medium">/</span>{{ organization.name }}</span>
            </span>
        </h1>

        <ReportNavbar />

        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="form-select-icon relative px-4 py-3 w-full rounded bg-white focus:shadow-outline appearance-none">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
            </div>
            <div class="flex mt-2 md:mt-0">
                <a target="_blank" :href="`/reports/export-index?` + getUrlParams()" class="flex items-center justify-end mr-2 p-2 text-white text-xs font-medium leading-5 bg-indigo-400 hover:bg-indigo-500 rounded-lg focus:outline-none duration-200">
                    <icon name="export" class="w-5 h-4" />
                </a>
                <button @click="search.modal = true" class="btn-gray">
                    <span>Фильтр/Поиск</span>
                </button>
            </div>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto" id="table_to_print">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-12 border-r" rowspan="2">#</th>
                    <th class="px-4 py-3 w-1/2 border-r" rowspan="2">Поставщик</th>
                    <th class="px-4 py-3 w-1/2 border-r" colspan="3">Накладные</th>
                </tr>
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 border-r">Оплачен</th>
                    <th class="px-4 py-3 border-r">Не оплачен</th>
                    <th class="px-4 py-3 border-r">Сумма</th>
                </tr>
                <tr v-for="(report, index) in reports" :key="report.supplier_id" class="hover:bg-amber-50 focus:bg-amber-50 duration-150">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <button @click="link(report.id, report.supplier_id, 'all')" class="flex items-center px-4 py-1 hover:underline font-medium">
                            {{ report.supplier }}
                        </button>
                    </td>
                    <td class="border-l border-t">
                        <button @click="link(report.id, report.supplier_id, 'pay')" class="flex items-center px-4 py-1 text-green-600 hover:underline whitespace-nowrap font-semibold">
                            {{ report.pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </button>
                    </td>
                    <td class="border-l border-t">
                        <button @click="link(report.id, report.supplier_id, 'not_pay')" class="flex items-center px-4 py-1 text-red-600 hover:underline whitespace-nowrap font-semibold">
                            {{ report.not_pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </button>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1 whitespace-nowrap font-semibold" :href="`/reports/${report.id}/${report.supplier_id}/not_pay`">
                            {{ (report.pay_sum + report.not_pay_sum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="reports.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="2">ИТОГО</td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ not_sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ (parseFloat(sum_pay) + parseFloat(not_sum_pay)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="reports.length === 0">
                    <td class="px-4 py-3 border-t" colspan="3">Не найдено.</td>
                </tr>
            </table>
        </div>
        <!-- <pagination class="mt-6" :links="reports.links" /> -->
        <ModalLeft @serach="seatch" @close="search.modal = !search.modal" :isOpen="search.modal">
            <ul role="list" class="-my-6">
                <li class="pb-4">
                    <text-input v-model="search.search" class="w-full" label="Название" />
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата от:</label>
                        <date-picker v-model="search.begin" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата до:</label>
                        <date-picker v-model="search.end" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
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
import SelectInput from '@/Shared/SelectInput'
import ModalLeft from '@/Shared/ModalLeft'
import ReportNavbar from './ReportNavbar'
import { Calendar, DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'
// import htmlToPdf from '@/htmlToPdf'

import pdfMake from 'pdfmake'
import pdfFonts from 'pdfmake/build/vfs_fonts'
import htmlToPdfmake from 'html-to-pdfmake'

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
        reports: Object,
        organizations: Object,
        sum_pay: Number,
        not_sum_pay: Number,
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
            },
            search: {
                modal: false,
                search: this.filters.search,
                begin: this.filters.begin,
                end: this.filters.end,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports', pickBy({ ...this.form, ...this.search }), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
            this.search = mapValues(this.search, () => null)
        },
        seatch() {
            this.$inertia.get(`/reports`, pickBy({ ...this.form, ...this.search }), { preserveState: true })
            this.search.modal = false
        },
        link(organization, supplier, param) {
            this.$inertia.get(`/reports/${organization}/${supplier}/${param}`, pickBy({ ...this.search }), { preserveState: true })
        },
        printDocument() {
            const pdfTable = document.getElementById('table_to_print')
            //html to pdf format
            var html = htmlToPdfmake(pdfTable.innerHTML)

            const documentDefinition = { content: html }
            pdfMake.vfs = pdfFonts.pdfMake.vfs
            pdfMake.createPdf(documentDefinition).open()
        },
        getUrlParams() {
            let begin = ''
            let end = ''
            if (this.search.begin) {
                let d = new Date(this.search.begin)
                begin = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
            }
            if (this.search.end) {
                let d = new Date(this.search.end)
                end = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
            }

            return new URLSearchParams({ organization_id: this.form.organization_id, begin: begin, end: end }).toString()
        },
    },
}
</script>
