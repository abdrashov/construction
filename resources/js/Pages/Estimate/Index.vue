<template>
    <div>
        <Head title="Смета" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="font-medium text-sky-500">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-sky-500">/</span>
            Смета
        </h1>
        
        <div class="flex items-center justify-between mt-6 mb-3 text-xl">
            <div class="w-full max-w-md mr-4">
                <h2 class="font-semibold text-gray-600">Товары</h2>
            </div>
            <div class="lg:flex">
                <button @click="openModal()" class="mb-1 btn-blue">
                    <span>Добавить</span>
                    <span class="hidden md:inline">&nbsp;Товар</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-4 py-3 border-l">Название</th>
                    <th class="px-4 py-3 border-l" colspan="2">Количество</th>
                </tr>
                <tr v-for="(estimate, index) in estimates" :key="estimate.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ index+1 }}
                        </div>
                    </td>
                    <td class="w-2/6 border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ estimate.name }}
                        </div>
                    </td>
                    <td class="w-1/6 border-t border-l">
                        <div class="flex items-center px-4 py-1">
                            <input v-maska="'#*.####'" v-model.lazy="estimate.count" @change="countHandler(index)" :name="`estimate[${index}][count]`" class="mr-1 border-b border-blue-300 border-dashed" />
                            <span>{{ estimate.measurement }}</span>
                        </div>
                    </td>
                    <td class="w-16 border-t border-l">
                        <div class="flex items-center justify-end px-4 py-1">
                            <button
                                type="submit"
                                @click.prevent="deleteItem(estimate.id)"
                                class="flex items-center justify-end px-2 py-2 text-sm font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-red-400 hover:bg-red-100 focus:outline-none"
                                aria-label="Delete"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="estimates.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Товары не найдены.</td>
                </tr>
            </table>
        </div>
        <div class="flex items-center justify-end mt-6 mb-3 text-xl">
            <button @click.prevent="store()" type="submit" class="mb-1 mr-2 btn-green">
                <span>Сохранить</span>
                <span class="hidden md:inline">&nbsp;Смету</span>
            </button>
        </div>

        <Modal @close="create.modal = !create.modal" :isOpen="create.modal" :width="create.width">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="flex flex-col w-full pr-10 font-medium text-left mw-auto">Добавить Товар</div>
                    <div class="flex items-center flex-shrink-0 ml-auto mr-0 space-x-4 font-bold text-indigo-900">
                        <button @click="create.modal = false" type="submit" class="p-2 text-gray-800 duration-200 bg-gray-100 rounded-full hover:text-gray-50 focus:text-gray-50 hover:bg-gray-500 focus:bg-gray-500">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <input class="relative w-full px-6 py-3 border focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="form_search.search" @input="$emit('update:modelValue', $event.target.value)" />
                <div class="mb-5">
                    <div class="text-sm bg-white border">
                        <table class="w-full">
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="p-2 px-4">Название</th>
                                <th class="p-2 px-4 text-right">Измерения</th>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    <div class="h-64 overflow-y-auto">
                                        <table class="w-full">
                                            <tr v-for="item in items.data" :key="item.id" class="duration-200 hover:bg-gray-100">
                                                <td class="border-t">
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 font-medium text-gray-900" @click.prevent="storeItem(item.id)">
                                                        {{ item.name }}
                                                    </button>
                                                </td>
                                                <td class="border-t">
                                                    <button type="submit" class="flex items-center justify-end w-full px-4 py-2" @click.prevent="storeItem(item.id)">
                                                        {{ item.measurement }}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="items.data.length === 0">
                                                <td class="px-6 py-4 border-t" colspan="2">Товары не найдены.</td>
                                            </tr>
                                        </table>
                                    </div>
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
                <pagination class="mt-6" :links="items.links" />
            </div>
        </Modal>
    </div>
</template>

<script>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Modal from '@/Shared/Modal'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import FileInput from '@/Shared/FileInput'
import pickBy from 'lodash/pickBy'
import Pagination from '@/Shared/Pagination'
import throttle from 'lodash/throttle'
import { Calendar, DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'
import { maska } from 'maska'

export default {
    directives: {
        maska,
    },
    components: {
        Head,
        Icon,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        TrashedMessage,
        Modal,
        FileInput,
        Calendar,
        DatePicker,
        Pagination,
    },
    layout: Layout,
    props: {
        filters: Object,
        organization: Object,
        estimates: Object,
        items: Object,
    },
    remember: ['form', 'form_invoice'],
    data() {
        return {
            form_search: {
                search: this.filters.search,
            },
            handler: {
                price: '',
                count: '',
            },
            create: {
                modal: this.filters.page ? true : false,
                width: 'sm:max-w-4xl',
            },
            form: this.$inertia.form({
                name: null,
                count: null,
                price: null,
            }),
        }
    },
    watch: {
        form_search: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(`/organizations/${this.organization.id}/estimates`, pickBy(this.form_search), { preserveState: true })
            }, 150),
        },
    },
    methods: {
        storeItem(item_id) {
            this.$inertia.post(`/organizations/${this.organization.id}/estimates/${item_id}`)
        },
        store() {
            this.$inertia.post(
                `/organizations/${this.organization.id}/estimates`,
                pickBy({
                    _method: 'put',
                    estimates: this.estimates,
                }),
                { preserveState: true },
            )
        },
        openModal() {
            this.store()
            this.create.modal = true
        },
        deleteItem(item_id) {
            this.store()
            this.$inertia.post(`/invoices/${this.invoice.id}/invoice-items`, pickBy({ _method: 'delete', item_id: item_id }), { preserveState: true })
        },
        countHandler(index) {
            if (this.estimates[index].count.toString()[0] == '.') {
                this.estimates[index].count = '0' + this.estimates[index].count
            }
            if (this.estimates[index].count == '') {
                this.estimates[index].count = 0
            }
            if (this.estimates[index].count > 5000000000) {
                this.estimates[index].count = 5000000000
                this.$swal.fire({
                    icon: 'error',
                    title: 'Упс...',
                    text: 'Лимит превышен, максимальное число - 5000000000!',
                })
            } else if (this.estimates[index].count < 0) {
                this.estimates[index].count = 0
                this.$swal.fire({
                    icon: 'error',
                    title: 'Упс...',
                    text: 'Число не может быть меньше 0!',
                })
            }
        },
    },
}
</script>
