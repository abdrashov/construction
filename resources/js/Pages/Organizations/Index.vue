<template>
    <div>
        <Head title="Объекты" />
        <h1 class="mb-6 text-2xl font-bold">Объекты</h1>
        <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="w-full mt-1 form-select">
                    <option :value="null" />
                    <option value="with">Все</option>
                    <option value="only">Только Удаленные</option>
                </select>
            </search-filter>
            <Link class="btn-indigo" href="/organizations/create">
                <span>Создать</span>
                <span class="hidden md:inline">&nbsp;Объект</span>
            </Link>
        </div>
        <div class="overflow-x-auto text-sm bg-white rounded-md shadow">
            <table class="w-full">
                <thead>
                    <tr class="font-bold text-left">
                        <th class="px-5 pt-5 pb-3">Название</th>
                        <th class="px-5 pt-5 pb-3">Адресс</th>
                        <th class="px-5 pt-5 pb-3" colspan="2">Дата создания</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="organization in organizations.data" :key="organization.id">
                        <td class="border-t">
                            <Link :href="`/organizations/${organization.id}/invoices`" class="flex items-center px-5 py-2">
                                {{ organization.name }}
                                <icon v-if="organization.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                            </Link>
                        </td>
                        <td class="border-t">
                            <Link :href="`/organizations/${organization.id}/invoices`" class="flex items-center px-5 py-2">
                                {{ organization.address }}
                            </Link>
                        </td>
                        <td class="border-t">
                            <Link :href="`/organizations/${organization.id}/invoices`" class="flex items-center px-5 py-2">
                                {{ organization.created_at }}
                            </Link>
                        </td>
                        <td class="w-px border-t">
                            <div class="flex pr-2">
                                <Link class="flex items-center justify-end px-2 py-2 text-sm font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-orange-400 hover:bg-orange-100 focus:outline-none" :href="`/organizations/${organization.id}/invoices`" tabindex="-1">
                                    <icon name="cheveron-right" class="block w-4 h-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="organizations.data.length === 0">
                        <td class="px-6 py-4 border-t" colspan="4">Объекты не найдено.</td>
                    </tr>
                </tbody>
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
import SearchFilter from '@/Shared/SearchFilter'

export default {
    components: {
        Head,
        Icon,
        Link,
        Pagination,
        SearchFilter,
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
                this.$inertia.get('/organizations', pickBy(this.form), { preserveState: true })
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
