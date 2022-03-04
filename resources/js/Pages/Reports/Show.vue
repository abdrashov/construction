<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/reports">Отчеты</Link>
            <span class="text-sky-500 font-medium">/</span>
            {{ organization.name }}
        </h1>
        <div class="flex items-center mb-6">
            <div class="w-full md:flex md:w-3/4 md:rounded md:shadow">
                <input class="relative px-4 py-3 w-full focus:shadow-outline md:rounded-l" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
            </div>
            <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div v-if="invoices.length < 1" class="flex items-center justify-between mb-3 mt-6 text-lg">
            <div class="mr-4 w-full max-w-md">
                <h2 class="text-gray-600 font-semibold">Накладные отсутствует</h2>
            </div>
        </div>
        <div v-for="invoice in invoices" :key="invoice.id">
            <div class="flex items-center justify-between mb-3 mt-6 text-lg">
                <div class="mr-4 w-full max-w-md">
                    <h2 class="text-gray-600 font-semibold">{{ invoice.name }}</h2>
                </div>
            </div>
            <div class="bg-white shadow">
                <div class="flex flex-wrap p-4">
                    <div class="mb-2 w-1/3 text-gray-600 text-sm text-xs font-semibold">Статус:</div>
                    <div class="mb-2 w-2/3 font-medium">
                        <div v-if="invoice.pay" class="text-green-700">Оплачен</div>
                        <div v-else class="text-red-700">Не оплачен</div>
                    </div>
                    <div class="mb-2 w-1/3 text-gray-600 text-sm text-xs font-semibold">Зав склад</div>
                    <div class="mb-2 w-2/3 font-medium">{{ invoice.accepted }}</div>
                    <div class="w-1/3 text-gray-600 text-sm text-xs font-semibold">Дата</div>
                    <div class="w-2/3 font-medium">{{ invoice.date }}</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                            <th class="px-4 py-3">Товар</th>
                            <th class="px-4 py-3 border-l">Количество</th>
                            <th class="px-4 py-3 border-l">Цена</th>
                            <th class="px-4 py-3 border-l" colspan="2">Сумма</th>
                        </tr>
                        <tr v-for="invoice_item in invoice.invoice_items" :key="invoice_item.id">
                            <td class="w-2/6 border-t">
                                <div class="flex items-center px-4 py-1 text-gray-900 font-medium">
                                    {{ invoice_item.name }}
                                </div>
                            </td>
                            <td class="w-1/6 border-l border-t">
                                <div class="flex items-center px-4 py-1 text-gray-900 font-medium">
                                    <span class="mr-1">{{ invoice_item.count }}</span>
                                    <span>{{ invoice_item.measurement }}</span>
                                </div>
                            </td>
                            <td class="w-1/6 border-l border-t">
                                <div class="flex items-center px-4 py-1 text-gray-900 font-medium">
                                    {{ invoice_item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                            <td class="w-2/6 border-l border-t">
                                <div class="flex items-center px-4 py-1 w-full whitespace-nowrap font-medium">
                                    {{ (invoice_item.count * invoice_item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                        </tr>
                        <tr class="font-semibold tracking-wide bg-cyan-50">
                            <td class="w-2/6 border-t pr-2">
                                <div class="flex justify-end">Сумма</div>
                            </td>
                            <td class="w-1/6 border-l border-t">
                                <div class="flex items-center px-4 py-3 text-gray-900 font-medium">
                                    <span class="mr-1">{{ invoice.sum_count }}</span>
                                </div>
                            </td>
                            <td class="w-1/6 border-l border-t">
                                <div class="flex items-center px-4 py-3 text-gray-900 font-medium">
                                    {{ invoice.sum_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                            <td class="w-2/6 border-l border-t">
                                <div class="flex items-center px-4 py-3 w-full whitespace-nowrap font-medium">
                                    {{ invoice.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
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
