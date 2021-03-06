<template>
    <div>
        <Head title="Отчеты по поставщикам" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="'/reports?organization_id='+organization.id+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">Отчеты по поставщикам</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ organization.name }}
        </h1>

        <div class="flex items-center mb-6">
            <div class="w-full rounded shadow md:flex md:w-3/4">
                <input class="relative w-full px-4 py-3 rounded focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
            </div>
            <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <div class="flex items-center mb-1 mx-4 mt-2">
                <span class="font-medium title-font w-1/3 text-sm text-gray-500 tracking-wider">Объект:</span>
                <span class="font-medium title-font w-2/3 text-gray-900 text-base">{{ organization.name }}</span>
            </div>
            <div class="flex items-center mb-2 mx-4">
                <span class="font-medium title-font w-1/3 text-sm text-gray-500 tracking-wider">Поставщик:</span>
                <span class="font-medium title-font w-2/3 text-gray-900 text-base">{{ supplier.name }}</span>
            </div>
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-4 py-3 border-l">Номер накладной</th>
                    <th class="px-4 py-3 border-l">Зав склад</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                    <th class="px-4 py-3 border-l">Дата</th>
                    <th class="px-4 py-3 border-l">Статус</th>
                </tr>
                <tr v-for="(invoice, index) in invoices" :key="invoice.id" class="duration-150 hover:bg-amber-50 focus:bg-amber-50">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-1 font-medium hover:underline" :href="`/reports/${organization.id}/${supplier.id}/${invoice.id}/items?`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
                            {{ invoice.name }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-1 hover:underline" :href="`/reports/${organization.id}/${supplier.id}/${invoice.id}/items?`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
                            {{ invoice.accepted }}
                        </Link>
                    </td>
                    <td class="w-2/6 border-t border-l">
                        <Link class="flex items-center px-4 py-1 hover:underline whitespace-nowrap" :href="`/reports/${organization.id}/${supplier.id}/${invoice.id}/items?`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
                            {{ invoice.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-1 hover:underline" :href="`/reports/${organization.id}/${supplier.id}/${invoice.id}/items?`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
                            {{ invoice.date }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex mx-4 text-xs">
                            <div class="flex items-center mr-2">
                                <span v-if="invoice.pay" class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full whitespace-nowrap"> Оплачен </span>
                                <span v-else class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full whitespace-nowrap"> Не оплачен </span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="invoices.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-t border-l"  colspan="3">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Накладные не найдены.</td>
                </tr>
            </table>
        </div>
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
    },
    layout: Layout,
    props: {
        filters: Object,
        organization: Object,
        supplier: Object,
        invoices: Object,
        sum_pay: Number,
    },
    data() {
        return {
            form: {
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
                this.$inertia.get(`/reports/${this.organization.id}/${this.supplier.id}/all`, pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form.search = null
        },
    },
}
</script>
