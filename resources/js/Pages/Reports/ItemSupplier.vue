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
                    <th class="px-4 py-3 border-l">Поставщики</th>
                    <th class="px-4 py-3 border-l border-r">Использовался</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                    <th class="px-4 py-3 border-l border-r">Дата</th>
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
                        <div class="flex items-center px-4 py-1">{{ supplier.count }} {{ supplier.measurement }}</div>
                    </td>
                    <td class="w-2/6 border-l border-t">
                        <div class="flex items-center px-4 py-1 whitespace-nowrap">
                            {{ supplier.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-1 whitespace-nowrap text-xs font-semibold">
                            {{ supplier.date }}
                        </div>
                    </td>
                </tr>
                <tr v-if="suppliers.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-l border-t" colspan="3">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ sum_pay?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="suppliers.length === 0">
                    <td class="px-6 py-4 border-t" colspan="5">Не найдено.</td>
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
