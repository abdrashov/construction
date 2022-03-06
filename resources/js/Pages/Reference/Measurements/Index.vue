<template>
    <div>
        <Head title="Измерение" />
        <h1 class="mb-6 text-2xl font-semibold">Измерение</h1>
        <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block text-gray-700">Удаленные:</label>
                <select v-model="form.trashed" class="form-select mt-1 w-full">
                    <option :value="null" />
                    <option value="with">Все</option>
                    <option value="only">Только Удаленные</option>
                </select>
            </search-filter>
            <button @click="create.modal = true" class="btn-blue">
                <span>Создать</span>
            </button>
        </div>
        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3" colspan="2">Названия</th>
                </tr>
                <tr v-for="measurement in measurements.data" :key="measurement.id">
                    <td class="border-t">
                        <Link :href="`/reference/measurements/${measurement.id}/edit`" class="flex items-center px-4 py-2 font-medium">
                            {{ measurement.name }}
                            <icon v-if="measurement.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="w-16 border-t">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link
                                class="focus:shadow-outline-gray flex items-center justify-end ml-2 px-2 py-2 text-gray-500 hover:text-orange-400 text-xs font-medium leading-5 bg-gray-100 hover:bg-orange-100 rounded-lg focus:outline-none duration-200"
                                :href="`/reference/measurements/${measurement.id}/edit`"
                            >
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="measurements.data.length === 0">
                    <td class="px-4 py-4 border-t" colspan="2">Не найдено.</td>
                </tr>
            </table>
        </div>
        <pagination class="mt-6" :links="measurements.links" />
        <Modal @close="create.modal = !create.modal" :isOpen="create.modal">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="mw-auto flex flex-col pr-10 w-full text-left font-medium">Создать</div>
                    <div class="flex flex-shrink-0 items-center ml-auto mr-0 text-indigo-900 font-bold space-x-4">
                        <button @click="create.modal = false" type="submit" class="p-2 hover:text-gray-50 focus:text-gray-50 text-gray-800 bg-gray-100 hover:bg-gray-500 focus:bg-gray-500 rounded-full duration-200">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form @submit.prevent="store">
                    <text-input v-model="form_create.name" :error="form_create.errors.name" class="pb-5 w-full" label="Название" />
                    <loading-button :loading="form_create.processing" class="btn-blue" type="submit">Создать</loading-button>
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
        measurements: Object,
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
                name: null,
            }),
        }
    },
    remember: 'form_create',
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reference/measurements', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        store() {
            this.form_create.post('', {
                onSuccess: () => {
                    this.form_create.reset('name')
                },
            })
        },
    },
}
</script>
