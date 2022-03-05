<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="flex items-center mb-6">
            <div class="w-full md:flex md:w-3/4 md:rounded md:shadow">
                <input class="relative w-full px-4 py-3 focus:shadow-outline md:w-1/3 md:rounded-l" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form.search" />
                <select v-model="form.organization_id" class="relative w-full px-4 py-3 mt-2 appearance-none form-select-icon focus:shadow-outline md:mt-0 md:w-2/3 md:border-l md:rounded-r">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
            </div>
            <button class="hidden w-8 ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500 md:block" type="button" @click="reset">Сброс</button>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3 border-r" rowspan="2">#</th>
                    <th class="w-1/2 px-4 py-3 border-r" rowspan="2">Поставщик</th>
                    <th class="w-1/2 px-4 py-3 border-r" colspan="2">Накладные</th>
                </tr>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 border-r">Оплачен</th>
                    <th class="px-4 py-3 border-r">Не оплачен</th>
                </tr>
                <tr v-for="(report, index) in reports" :key="report.id" class="duration-150 hover:bg-amber-50 focus:bg-amber-50">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-2 font-medium hover:underline" :href="`/reports/${report.id}/${report.supplier_id}/all`">
                            {{ report.supplier }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-2 font-semibold text-green-900 hover:underline whitespace-nowrap" :href="`/reports/${report.id}/${report.supplier_id}/all?pay=1`">
                            {{ report.pay_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-4 py-2 font-semibold text-red-900 hover:underline whitespace-nowrap" :href="`/reports/${report.id}/${report.supplier_id}/all?pay=0000`">
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
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>
