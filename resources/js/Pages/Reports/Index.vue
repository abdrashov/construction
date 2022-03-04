<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="flex items-center mb-6">
            <div class="w-full md:rounded md:shadow md:flex md:w-3/4">
                <input class="relative px-4 py-3 w-full md:rounded-l focus:shadow-outline md:w-1/3" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
                <select v-model="form.organization_id" class="form-select-icon relative mt-2 px-4 py-3 w-full md:border-l md:rounded-r focus:shadow-outline appearance-none md:mt-0 md:w-2/3">
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
            </div>
            <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-1/2 border-r" rowspan="2">Название</th>
                    <th class="px-4 py-3 w-1/2 border-r" colspan="2">Накладные</th>
                </tr>
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 border-r">Оплачен</th>
                    <th class="px-4 py-3 border-r">Не оплачен</th>
                </tr>
                <tr v-for="report in reports" :key="report.id">
                    <td class="border-t">
                        <Link class="flex items-center font-medium px-4 py-2" :href="`/reports/${report.id}/${report.supplier_id}/all`">
                            {{ report.supplier }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-4 py-2 whitespace-nowrap font-semibold" :href="`/reports/${report.id}/${report.supplier_id}/all?pay=1`">
                            {{ report.pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-4 py-2 whitespace-nowrap font-semibold" :href="`/reports/${report.id}/${report.supplier_id}/all?pay=0000`">
                            {{ report.not_pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                </tr>
                <tr v-if="reports.length === 0">
                    <td class="px-4 py-4 border-t" colspan="3">Не найдено.</td>
                </tr>
            </table>
        </div>
        <!-- <pagination class="mt-6" :links="reports.links" /> -->
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
        reports: Object,
        organizations: Object,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                organization_id: this.filters.organization_id,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports', pickBy(this.form), { preserveState: true })
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
