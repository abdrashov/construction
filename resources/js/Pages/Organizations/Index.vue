<template>
    <div>
        <Head title="Объекты" />
        <h1 class="mb-6 text-2xl font-bold">Объекты</h1>
        <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="form-select mt-1 w-full">
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
                <Link :href="`/organizations/${organization.id}/invoices`">
                    <div class="flex flex-col hover:bg-gray-100 bg-white rounded-lg shadow-xl overflow-hidden duration-200">
                        <div class="h-36">
                            <img aria-hidden="true" class="w-full h-full object-cover object-center" src="/img/art_3.jpg" alt="" />
                        </div>
                        <div class="p-4">
                            <h1 class="title-font mb-3 text-gray-900 text-base font-medium">
                                {{ organization.name }}
                            </h1>
                            <p class="text-sm leading-relaxed">{{ organization.address }}.</p>
                            <div class="flex justify-end text-sm font-medium">
                                <div v-if="organization.deleted_at" class="mr-2 text-red-700">
                                    {{ organization.deleted_at }}
                                </div>
                                <div>
                                    {{ organization.created_at }}
                                </div>
                            </div>
                        </div>
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
