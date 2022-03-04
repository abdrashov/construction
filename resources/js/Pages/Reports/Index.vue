<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">Отчеты</h1>
        <div class="flex justify-between mb-6">
            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="w-full mt-1 form-select">
                    <option :value="null" />
                    <option value="with">Все</option>
                    <option value="only">Только Удаленные</option>
                </select>
            </search-filter>
        </div>
        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 border-r" rowspan="2">Название</th>
                    <th class="px-4 py-3 border-r" colspan="2">Накладные</th>
                    <th class="px-4 py-3 border-r" colspan="2">Товары</th>
                    <th class="px-4 py-3" rowspan="2">Создано</th>
                </tr>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3 border-r">Подтвержден</th>
                    <th class="px-4 py-3 border-r">Не подтвержден</th>
                    <th class="px-4 py-3 border-r">Количество</th>
                    <th class="px-4 py-3 border-r">Сума</th>
                </tr>
                <tr v-for="organization in organizations.data" :key="organization.id">
                    <td class="border-t">
                        <div class="flex px-4 py-2 font-medium">
                            {{ organization.name }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 text-xs">
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                            {{ organization.confirmed }}
                             </span>
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 text-xs">
                            <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">
                            {{ organization.not_confirmed }}
                             </span>
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 font-semibold">
                            {{ organization.sum_count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                             </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 font-semibold">
                            {{ organization.sum_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 whitespace-nowrap">
                            {{ organization.created_at }}
                        </div>
                    </td>
                </tr>
                <tr v-if="organizations.data.length === 0">
                    <td class="px-4 py-4 border-t" colspan="4">Не найдено.</td>
                </tr>
            </table>
        </div>
        <pagination class="mt-6" :links="organizations.links" />
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import LoadingButton from '@/Shared/LoadingButton'
import SearchFilter from '@/Shared/SearchFilter'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'

export default {
    components: {
        Head,
        Icon,
        Link,
        Pagination,
        SearchFilter,
        TextInput,
        LoadingButton,
        SelectInput,
    },
    layout: Layout,
    props: {
        filters: Object,
        organizations: Object,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
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
