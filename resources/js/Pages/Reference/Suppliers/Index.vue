<template>
    <div>
        <Head title="Поставщики" />
        <h1 class="mb-6 text-2xl font-bold">Поставщики</h1>
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
        <div class="text-sm bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                        <th class="px-4 py-3">Название</th>
                    </tr>
                    <tr v-for="supplier in suppliers.data" :key="supplier.id">
                        <td class="border-t">
                            <Link :href="`/reference/suppliers/${supplier.id}/edit`" class=" font-medium flex items-center px-4 py-2">
                                {{ supplier.name }}
                                <icon v-if="supplier.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="suppliers.data.length === 0">
                        <td class="px-4 py-4 border-t" colspan="4">Не найдено.</td>
                    </tr>
            </table>
        </div>
        <pagination class="mt-6" :links="suppliers.links" />
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
                    <text-input v-model="form_create.name" :error="form_create.errors.name" class="w-full pb-5" label="Название" />
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
        suppliers: Object,
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
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get('/reference/suppliers', pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    remember: 'form_create',
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
