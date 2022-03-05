<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/reports">Отчеты</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ organization.name }}
        </h1>
        <div class="flex items-center mb-6">
            <div class="w-full md:flex md:w-3/4 md:rounded md:shadow">
                <input class="relative w-full px-4 py-3 focus:shadow-outline md:rounded-l" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
            </div>
            <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Номер накладной</th>
                    <th class="px-4 py-3 border-l">Принял</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                    <th class="px-4 py-3 border-l">Дата</th>
                    <th class="px-4 py-3 border-l">Статусы</th>
                </tr>
                <tr v-for="invoice in invoices" :key="invoice.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-3 font-medium text-gray-900">
                            {{ invoice.name }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-3 border-l">
                            {{ invoice.accepted }}
                        </div>
                    </td>
                    <td class="w-2/6 border-t border-l">
                        <div class="flex items-center w-full px-4 py-3 font-medium whitespace-nowrap">
                            {{ invoice.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-3 border-l">
                            {{ invoice.date }}
                        </div>
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
                <tr v-if="invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="5">Накладные не найдены.</td>
                </tr>
            </table>
        </div>

        <!-- <div v-for="invoice in invoices" :key="invoice.id">
            <div class="flex items-center justify-between mt-6 mb-3 text-lg">
                <div class="w-full max-w-md mr-4">
                    <h2 class="font-semibold text-gray-600">{{ invoice.name }}</h2>
                </div>
            </div>
            <div class="bg-white shadow">
                <div class="flex flex-wrap p-4">
                    <div class="w-1/3 mb-2 text-xs text-sm font-semibold text-gray-600">Статус:</div>
                    <div class="w-2/3 mb-2 font-medium">
                        <div v-if="invoice.pay" class="text-green-700">Оплачен</div>
                        <div v-else class="text-red-700">Не оплачен</div>
                    </div>
                    <div class="w-1/3 mb-2 text-xs text-sm font-semibold text-gray-600">Зав склад</div>
                    <div class="w-2/3 mb-2 font-medium">{{ invoice.accepted }}</div>
                    <div class="w-1/3 text-xs text-sm font-semibold text-gray-600">Дата</div>
                    <div class="w-2/3 font-medium">{{ invoice.date }}</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Товар</th>
                            <th class="px-4 py-3 border-l">Количество</th>
                            <th class="px-4 py-3 border-l">Цена</th>
                            <th class="px-4 py-3 border-l" colspan="2">Сумма</th>
                        </tr>
                        <tr v-for="invoice_item in invoice.invoice_items" :key="invoice_item.id">
                            <td class="w-2/6 border-t">
                                <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                                    {{ invoice_item.name }}
                                </div>
                            </td>
                            <td class="w-1/6 border-t border-l">
                                <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                                    <span class="mr-1">{{ invoice_item.count }}</span>
                                    <span>{{ invoice_item.measurement }}</span>
                                </div>
                            </td>
                            <td class="w-1/6 border-t border-l">
                                <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                                    {{ invoice_item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                            <td class="w-2/6 border-t border-l">
                                <div class="flex items-center w-full px-4 py-1 font-medium whitespace-nowrap">
                                    {{ (invoice_item.count * invoice_item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                        </tr>
                        <tr class="font-semibold tracking-wide bg-cyan-50">
                            <td class="w-2/6 pr-2 border-t">
                                <div class="flex justify-end">Сумма</div>
                            </td>
                            <td class="w-1/6 border-t border-l">
                                <div class="flex items-center px-4 py-3 font-medium text-gray-900">
                                    <span class="mr-1">{{ invoice.sum_count }}</span>
                                </div>
                            </td>
                            <td class="w-1/6 border-t border-l">
                                <div class="flex items-center px-4 py-3 font-medium text-gray-900">
                                    {{ invoice.sum_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                            <td class="w-2/6 border-t border-l">
                                <div class="flex items-center w-full px-4 py-3 font-medium whitespace-nowrap">
                                    {{ invoice.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div> -->
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
    },
    data() {
        return {
            form: {
                search: this.filters.search,
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
