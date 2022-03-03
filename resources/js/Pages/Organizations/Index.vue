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
        <div class="flex flex-wrap -m-4">
            <div v-for="organization in organizations.data" :key="organization.id" class="p-4 sm:w-1/2 lg:w-1/3">
                <Link :href="`/organizations/${organization.id}/invoices`" :class="(!organization.deleted_at ? 'bg-white hover:bg-gray-100' : 'bg-red-50 hover:bg-red-300') + ' block w-full p-6 duration-200 border-gray-300 border-4 rounded-md '">
                    <h1 class="mb-3 text-lg font-medium text-gray-900 title-font">
                        {{ organization.name }}
                    </h1>
                    <p class="leading-relaxed">{{ organization.address }}.</p>
                    <div class="text-sm font-medium text-right">
                        {{ organization.created_at }}
                    </div>
                </Link>
            </div>
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
