<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="mb-2">
            <Link href="/reports" class="inline-block mt-2 btn-blue md:mt-0">
                <span>По поставщикам</span>
            </Link>
            <Link href="/reports/items" class="inline-block mt-2 ml-0 btn-blue md:ml-2 md:mt-0">
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
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-10 px-4 py-3 border-r">#</th>
                    <th class="px-4 py-3 border-l border-r">Название Товара</th>
                    <th class="px-4 py-3 border-l border-r">Использовался</th>
                    <th class="px-4 py-3 border-l border-r">Сумма</th>
                    <th class="px-4 py-3 border-l border-r">Итого</th>
                </tr>
                <tr v-for="item in items" :key="item.id">
                    <td class="border-t" :colspan="item.count ? 1 : 5" :class="(!item.count ? 'bg-sky-200' : '')">
                        <div :class="'flex items-center px-4 py-1 ' + (item.count ? 'font-medium' : 'font-semibold')">
                            {{ item.count ? item.index : item.name }}
                        </div>
                    </td>
                    <td v-if="item.count" class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ item.name }}
                        </div>
                    </td>
                    <td v-if="item.count" class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium">
                            {{ item.count }}
                            {{ item.measurement }}
                        </div>
                    </td>
                    <td v-if="item.count" class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold text-green-600 whitespace-nowrap">
                            {{ item.sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td v-if="item.count" class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ item.category_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="3">ИТОГО</td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_item?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ sum_item_category?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td class="px-4 py-3 border-t" colspan="5">Не найдено.</td>
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
        sum_item_category: Number,
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
