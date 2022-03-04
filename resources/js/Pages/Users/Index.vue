<template>
    <div>
        <Head title="Пользователи" />

        <h1 class="mb-6 text-2xl font-semibold">Пользователи</h1>

        <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="w-full mt-1 form-select">
                    <option :value="null" />
                    <option value="with">Все</option>
                    <option value="only">Только Удаленные</option>
                </select>
            </search-filter>
            <Link class="btn-blue" href="/users/create">
                <span>Создать</span>
                <span class="hidden md:inline">&nbsp;Пользователя</span>
            </Link>
        </div>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Фамилия</th>
                    <th class="px-4 py-3">Имя</th>
                    <th class="px-4 py-3">Логин</th>
                    <th class="px-4 py-3" colspan="2">Роль</th>
                </tr>
                <tr v-for="user in users" :key="user.id">
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/users/${user.id}/edit`">
                            {{ user.lastname }}
                            <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/users/${user.id}/edit`" tabindex="-1">
                            {{ user.firstname }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/users/${user.id}/edit`" tabindex="-1">
                            {{ user.login }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/users/${user.id}/edit`" tabindex="-1">
                            {{ user.role }}
                        </Link>
                    </td>
                    <td class="w-16 border-t">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link class="flex items-center justify-end px-2 py-2 ml-2 text-xs font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-orange-400 hover:bg-orange-100 focus:outline-none" :href="`/users/${user.id}/edit`">
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="users.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Пользователи не найдены.</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'

export default {
    components: {
        Head,
        Icon,
        Link,
        SearchFilter,
    },
    layout: Layout,
    props: {
        filters: Object,
        users: Array,
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
                this.$inertia.get('/users', pickBy(this.form), { preserveState: true })
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
