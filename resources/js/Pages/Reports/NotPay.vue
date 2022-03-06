<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="`/reports?organization_id=${organization.id}`">Отчеты</Link>
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
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-4 py-3 border-l">Номер накладной</th>
                    <th class="px-4 py-3 border-l">Зав склад</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                    <th class="px-4 py-3 border-l">Дата</th>
                </tr>
                <tr v-for="(invoice, index) in invoices" :key="invoice.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-3 font-semibold border-l">
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
                </tr>
                <tr v-if="invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="5">Накладные не найдены.</td>
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
