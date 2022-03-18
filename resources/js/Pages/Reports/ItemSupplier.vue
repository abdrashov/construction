<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="'/reports/items?organization_id=' + organization.id + (form.begin ? '&begin=' + form.begin : '') + (form.end ? '&end=' + form.end : '')">Отчеты</Link>
            <span class="text-sky-500 font-medium">/</span>
            {{ organization.name }}
        </h1>
        <div class="flex items-center mb-6">
            <div class="w-full rounded shadow md:flex md:w-3/4">
                <input class="relative px-4 py-3 w-full rounded focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
            </div>
            <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-4 py-3 border-l">Поставщик</th>
                    <th class="px-4 py-3 border-l border-r">Накладной</th>
                    <th class="px-4 py-3 border-l border-r">Дата</th>
                    <th class="px-4 py-3 border-l border-r">Количество</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                </tr>
                <tr v-for="(supplier, index) in suppliers" :key="supplier.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ supplier.name }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1">
                            {{ supplier.invoice }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1 whitespace-nowrap">
                            {{ supplier.date }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1  font-medium">{{ supplier.count }} {{ supplier.measurement }}</div>
                    </td>
                    <td class="w-2/6 border-l border-t">
                        <div class="flex items-center px-4 py-1 whitespace-nowrap  font-medium">
                            {{ supplier.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="suppliers.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="4">ИТОГО</td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ count_pay }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="suppliers.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Не найдено.</td>
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
        suppliers: Object,
        sum_pay: Number,
        count_pay: Number,
        item_id: String,
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
                this.$inertia.get(`/reports/items/${this.organization.id}/${this.item_id}/supplier`, pickBy(this.form), { preserveState: true })
            }, 250),
        },
    },
    methods: {
        reset() {
            this.form.search = null
        },
    },
}
</script>
