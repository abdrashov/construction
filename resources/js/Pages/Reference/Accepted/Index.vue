<template>
    <div>
        <Head title="Принимающие" />
        <h1 class="mb-8 text-3xl font-bold">Принимающие</h1>
        <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="w-full mt-1 form-select">
                    <option :value="null" />
                    <option value="with">Все</option>
                    <option value="only">Только Удаленные</option>
                </select>
            </search-filter>
            <button @click="create.modal = true" class="btn-indigo">
                <span>Создать</span>
            </button>
        </div>
        <div class="overflow-x-auto bg-white rounded-md shadow">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="font-bold text-left">
                        <th class="px-6 pt-6 pb-4">Фамилия</th>
                        <th class="px-6 pt-6 pb-4">Имя</th>
                        <th class="px-6 pt-6 pb-4">Отчество</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="accepted in accepteds.data" :key="accepted.id">
                        <td class="border-t">
                            <Link :href="`/reference/accepted/${accepted.id}/edit`" class="flex items-center px-6 py-4">
                                {{ accepted.lastname }}
                                <icon v-if="accepted.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                            </Link>
                        </td>
                        <td class="border-t">
                            <Link :href="`/reference/accepted/${accepted.id}/edit`" class="flex items-center px-6 py-4">
                                {{ accepted.firstname }}
                            </Link>
                        </td>
                        <td class="border-t">
                            <Link :href="`/reference/accepted/${accepted.id}/edit`" class="flex items-center px-6 py-4">
                                {{ accepted.middlename }}
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="accepteds.data.length === 0">
                        <td class="px-6 py-4 border-t" colspan="4">Не найдено.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination class="mt-6" :links="accepteds.links" />
        <Modal @close="create.modal = !create.modal" :isOpen="create.modal">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="flex flex-col w-full pr-10 font-medium text-left mw-auto">Создать</div>
                    <div class="flex items-center flex-shrink-0 ml-auto mr-0 space-x-4 font-bold text-indigo-900">
                        <button @click="create.modal = false" type="submit" class="p-2 text-gray-800 duration-200 bg-gray-100 rounded-full hover:text-gray-50 focus:text-gray-50 hover:bg-gray-500 focus:bg-gray-500">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form @submit.prevent="store">
                    <text-input v-model="form_create.lastname" :error="form_create.errors.lastname" class="w-full pb-5" label="Фамилия" />
                    <text-input v-model="form_create.firstname" :error="form_create.errors.firstname" class="w-full pb-5" label="Имя" />
                    <text-input v-model="form_create.middlename" :error="form_create.errors.middlename" class="w-full pb-5" label="Отчество" />

                    <loading-button :loading="form_create.processing" class="btn-indigo" type="submit">Создать</loading-button>
                </form>
            </div>
        </Modal>
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
import Modal from '@/Shared/Modal'
import TextInput from '@/Shared/TextInput'

export default {
    components: {
        Head,
        Icon,
        Link,
        Pagination,
        SearchFilter,
        Modal,
        TextInput,
        LoadingButton,
    },
    layout: Layout,
    props: {
        filters: Object,
        accepteds: Object,
    },
    data() {
        return {
            create: {
                modal: false,
            },
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
            },
            form_create: this.$inertia.form({
                lastname: null,
                firstname: null,
                middlename: null,
            }),
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
            this.$inertia.get('', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    remember: 'form',
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        store() {
            this.form_create.post('', {
                onSuccess: () => {
                    this.form_create.reset('lastname')
                    this.form_create.reset('firstname')
                    this.form_create.reset('middlename')
                },
            })
        },
    },
}
</script>
