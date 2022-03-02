<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="text-indigo-400 font-medium">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ invoice.name }}
        </h1>
        <div class="w-full lg:flex">
            <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
                <form @submit.prevent="update">
                    <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                        <text-input v-model="form_invoice.name" :error="form_invoice.errors.name" class="pb-5 pr-6 w-full" label="Название" />
                        <select-input v-model="form_invoice.supplier_id" :error="form_invoice.errors.supplier_id" class="pb-5 pr-6 w-full md:w-1/2" label="Поставщик">
                            <option :value="null"></option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select-input>
                        <select-input v-model="form_invoice.accepted_id" :error="form_invoice.errors.accepted_id" class="pb-5 pr-6 w-full md:w-1/2" label="Принял">
                            <option :value="null"></option>
                            <option v-for="accepted in accepteds" :key="accepted.id" :value="accepted.id">{{ accepted.lastname }} {{ accepted.firstname }}</option>
                        </select-input>
                        <div class="pb-5 pr-6 w-full md:w-1/2">
                            <label class="form-label">Дата:</label>
                            <Datepicker v-model="form_invoice.date" :format="date_format" locale="ru" cancelText="Отмена" selectText="Выбрать"></Datepicker>
                            <div v-if="form_invoice.errors.date" class="form-error">{{ form_invoice.errors.date }}</div>
                        </div>
                        <file-input v-model="form_invoice.file" :error="form_invoice.errors.file" class="pb-8 pr-6 w-full md:w-1/2" type="file" accept="file/*" label="Файл" />
                    </div>
                    <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
                        <loading-button :loading="form_invoice.processing" class="btn-indigo" type="submit">Обновить Накладной</loading-button>
                    </div>
                </form>
            </div>
            <div v-if="invoice.file" class="mt-4 w-full break-words bg-white rounded-md shadow lg:ml-4 lg:mt-0 lg:w-1/3">
                <div class="flex flex-wrap -mb-8 p-8">
                    <div class="w-20 text-center">
                        <img src="/pdf.svg" class="w-full" alt="" />
                        <a :href="invoice.file" target="_blank" class="pt-2 hover:underline"> Файл </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="items-center justify-between mb-6 mt-12 lg:flex">
            <div class="mr-4 w-full max-w-md">
                <h2 class="text-2xl font-bold">Товары</h2>
            </div>
            <div class="lg:flex">
                <button @click.prevent="confirm()" type="submit" class="mb-1 mr-2 px-6 py-3 text-white whitespace-nowrap text-sm font-bold leading-4 bg-green-600 hover:bg-orange-400 focus:bg-orange-400 rounded">
                    <span>Подтвердить</span>
                    <span class="hidden md:inline">&nbsp;Накладной</span>
                </button>
                <button @click.prevent="store()" type="submit" class="btn-indigo mb-1 mr-2">
                    <span>Сохранить</span>
                </button>
                <button @click="create.modal = true" class="btn-indigo mb-1">
                    <span>Добавить</span>
                    <span class="hidden md:inline">&nbsp;Товар</span>
                </button>
            </div>
        </div>

        <div class="mt-6 bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <tr class="text-left font-bold">
                    <th class="pb-4 pt-6 px-6">Название</th>
                    <th class="pb-4 pt-6 px-6">Количество</th>
                    <th class="pb-4 pt-6 px-6">Цена</th>
                    <th class="pb-4 pt-6 px-6" colspan="2">Сумма</th>
                </tr>
                <tr v-for="(item, index) in invoice_items" :key="item.id">
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4 focus:text-indigo-500">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            <input type="number" v-model="item.count" :name="`item[${index}][count]`" class="form-input w-full" />
                            {{ item.measurement }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            <input type="number" v-model="item.price" :name="`item[${index}][price]`" class="form-input w-full" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            {{ (item.count * item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center py-4">
                            <button
                                type="submit"
                                @click.prevent="deleteItem(item.id)"
                                class="focus:shadow-outline-gray flex items-center justify-end px-2 py-2 text-gray-500 hover:text-red-400 text-sm font-medium leading-5 bg-gray-100 hover:bg-red-100 rounded-lg focus:outline-none duration-200"
                                aria-label="Delete"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="5">Товары не найдены.</td>
                </tr>
            </table>
        </div>

        <Modal @close="create.modal = !create.modal" :isOpen="create.modal" :width="create.width">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="mw-auto flex flex-col pr-10 w-full text-left font-medium">Добавить Товар</div>
                    <div class="flex flex-shrink-0 items-center ml-auto mr-0 text-indigo-900 font-bold space-x-4">
                        <button @click="create.modal = false" type="submit" class="p-2 hover:text-gray-50 focus:text-gray-50 text-gray-800 bg-gray-100 hover:bg-gray-500 focus:bg-gray-500 rounded-full duration-200">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <input @keyup="searchItem" class="relative px-4 py-2 w-full border-b focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="search" />
                <div class="my-5 h-64 overflow-y-scroll">
                    <table class="w-full">
                        <tr class="text-left font-bold">
                            <th class="p-2 px-4">Название</th>
                            <th class="p-2 px-4 text-right">Измерения</th>
                        </tr>
                        <tr v-for="item in search_items" :key="item.id" class="hover:bg-gray-100 focus:bg-gray-100">
                            <td class="border-t">
                                <button type="submit" class="flex items-center px-4 py-2 w-full" @click.prevent="storeItem(item.id)">
                                    {{ item.name }}
                                </button>
                            </td>
                            <td class="border-t">
                                <button type="submit" class="flex items-center justify-end px-4 py-2 w-full" @click.prevent="storeItem(item.id)">
                                    {{ item.measurement }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td class="px-4 py-2 border-t" colspan="2">Товары не найдены.</td>
                        </tr>
                    </table>
                </div>
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
import throttle from 'lodash/throttle'
import Datepicker from 'vue3-date-time-picker'
import 'vue3-date-time-picker/dist/main.css'

export default {
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
        Datepicker,
    },
    layout: Layout,
    props: {
        organization: Object,
        invoice: Object,
        invoice_items: Object,
        items: Object,
        accepteds: Object,
        suppliers: Object,
    },
    remember: ['form', 'form_invoice'],
    data() {
        return {
            search: '',
            search_items: this.items,
            handler: {
                price: '',
                count: '',
            },
            create: {
                modal: false,
                width: 'sm:max-w-4xl',
            },
            form: this.$inertia.form({
                name: null,
                count: null,
                price: null,
            }),
            form_invoice: this.$inertia.form({
                _method: 'put',
                name: this.invoice.name,
                date: this.invoice.date,
                supplier_id: this.invoice.supplier_id,
                accepted_id: this.invoice.accepted_id,
                file: null,
            }),
        }
    },
    methods: {
        update() {
            if (this.form_invoice.date.toString().length > 10) {
                const day = this.form_invoice.date.getDate()
                const month = this.form_invoice.date.getMonth() + 1
                const year = this.form_invoice.date.getFullYear()

                this.form_invoice.date = `${year}-${month}-${day}`
            }
            this.form_invoice.post(`/organizations/${this.organization.id}/invoices/${this.invoice.id}`, {
                onSuccess: () => this.form_invoice.reset('file'),
            })
        },
        storeItem(item_id) {
            this.$inertia.post('', pickBy({ item_id: item_id }), { preserveState: true })
            this.create.modal = false
        },
        store() {
            this.$inertia.post(
                `/invoices/${this.invoice.id}/invoice-items`,
                pickBy({
                    _method: 'put',
                    items: this.invoice_items,
                }),
                { preserveState: true },
            )
        },
        confirm() {
            if (confirm('Вы уверены, что хотите подтвердить этот счет?')) {
                this.$inertia.post(
                    `/invoices/${this.invoice.id}/invoice-items/confirm`,
                    pickBy({
                        items: this.invoice_items,
                    }),
                    { preserveState: true },
                )
            }
        },
        searchItem() {
            this.search_items = this.items.filter((item) => {
                return item.name.includes(this.search)
            })
        },
        deleteItem(item_id) {
            this.$inertia.post(`/invoices/${this.invoice.id}/invoice-items`, pickBy({ _method: 'delete', item_id: item_id }), { preserveState: true })
        },
    },
    setup() {
        const date_format = (date) => {
            const day = date.getDate()
            const month = date.getMonth() + 1
            const year = date.getFullYear()
            return `${year}-${month}-${day}`
        }
        return {
            date_format,
        }
    },
}
</script>
