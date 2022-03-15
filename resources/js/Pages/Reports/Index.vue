<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="mb-2">
            <Link href="/reports" class="inline-block mt-2 md:mt-0 btn-blue">
                <span>По поставщикам</span>
            </Link>
            <Link href="/reports/items" class="inline-block mt-2 ml-0 md:mt-0 md:ml-2 btn-blue">
                <span>По товарам</span>
            </Link>
        </div>
        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="relative w-full px-4 py-3 rounded appearance-none form-select-icon focus:shadow-outline">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
            </div>
            <button @click="search.modal = true" class="mt-2 md:mt-0 btn-gray">
                <span>Фильтр/Поиск</span>
            </button>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3 border-r" rowspan="2">#</th>
                    <th class="w-1/2 px-4 py-3 border-r" rowspan="2">Поставщик</th>
                    <th class="w-1/2 px-4 py-3 border-r" colspan="3">Накладные</th>
                </tr>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 border-r">Оплачен</th>
                    <th class="px-4 py-3 border-r">Не оплачен</th>
                    <th class="px-4 py-3 border-r">Сумма</th>
                </tr>
                <tr v-for="(report, index) in reports" :key="report.id" class="duration-150 hover:bg-amber-50 focus:bg-amber-50">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <button @click="link(report.id, report.supplier_id, 'all')" class="flex items-center px-4 py-1 font-medium hover:underline">
                            {{ report.supplier }}
                        </button>
                    </td>
                    <td class="border-t border-l">
                        <button @click="link(report.id, report.supplier_id, 'pay')" class="flex items-center px-4 py-1 font-semibold text-green-600 hover:underline whitespace-nowrap">
                            {{ report.pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </button>
                    </td>
                    <td class="border-t border-l">
                        <button @click="link(report.id, report.supplier_id, 'not_pay')" class="flex items-center px-4 py-1 font-semibold text-red-600 hover:underline whitespace-nowrap">
                            {{ report.not_pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </button>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-semibold whitespace-nowrap" :href="`/reports/${report.id}/${report.supplier_id}/not_pay`">
                            {{ (report.pay_sum + report.not_pay_sum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="reports.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="2">ИТОГО</td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ not_sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ (sum_pay + not_sum_pay).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
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
            this.$inertia.get(`/reports/${organization}/${supplier}/${param}`, pickBy({...this.search }), { preserveState: true })
        }
    },
}
</script>
