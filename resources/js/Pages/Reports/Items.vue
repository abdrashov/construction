<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="mb-2">
            <Link href="/reports" class="btn-blue inline-block mt-2 md:mt-0">
                <span>По поставщикам</span>
            </Link>
            <Link href="/reports/items" class="btn-blue inline-block ml-0 mt-2 md:ml-2 md:mt-0">
                <span>По товарам</span>
            </Link>
        </div>
        <div class="items-center justify-between mb-6 md:flex">
            <div class="items-center w-full md:flex md:w-1/2">
                <select v-model="form.organization_id" class="form-select-icon relative px-4 py-3 w-full rounded focus:shadow-outline appearance-none">
                    <option :value="null">Выберите объект</option>
                    <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
                <button class="hidden ml-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
            </div>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-10 border-r">#</th>
                    <th class="px-4 py-3 border-l border-r">Название Товара</th>
                    <th class="px-4 py-3 border-l border-r">Использовался</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                </tr>
                <tr v-for="(item, index) in items" :key="item.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ item.name }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ item.count }} 
                            {{ item.measurement }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ item.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length !== 0">
                    <td class="px-4 py-4 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
                            {{ sum_item?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td class="px-4 py-4 border-t" colspan="3">Не найдено.</td>
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
        organizations: Object,
        items: Object,
        sum_item: Number,
    },
    data() {
        return {
            form: {
                organization_id: this.filters.organization_id,
                search: this.filters.search,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reports/items', pickBy(this.form), { preserveState: true })
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
