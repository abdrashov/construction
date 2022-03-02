<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="font-medium text-indigo-400">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ invoice.name }}
        </h1>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <text-input v-model="form_invoice.name" :error="form_invoice.errors.name" class="w-full pb-5 pr-6" label="Название" />
                    <text-input v-model="form_invoice.supplier" :error="form_invoice.errors.supplier" class="w-full pb-5 pr-6 md:w-1/2" label="Поставщик" />
                    <text-input v-model="form_invoice.accepted" :error="form_invoice.errors.accepted" class="w-full pb-5 pr-6 md:w-1/2" label="Принял" />
                    <text-input v-model="form_invoice.date" :error="form_invoice.errors.date" class="w-full pb-5 pr-6 md:w-1/2" label="Дата" />
                    <file-input v-model="form_invoice.file" :error="form_invoice.errors.file" class="w-full pb-8 pr-6 md:w-1/2" type="file" accept="file/*" label="Файл" />
                </div>
                <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100 bg-gray-50">
                    <loading-button :loading="form_invoice.processing" class="btn-indigo" type="submit">Обновить Накладной</loading-button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mt-12 mb-6">
            <div class="w-full max-w-md mr-4">
                <h2 class="text-2xl font-bold">Товары</h2>
            </div>
            <div class="flex">
                <button @click="create.modal = true" class="px-6 py-3 mr-2 text-sm font-bold leading-4 text-white bg-green-600 rounded whitespace-nowrap hover:bg-orange-400 focus:bg-orange-400">
                    <span>Подтвердить</span>
                    <span class="hidden md:inline">&nbsp;Накладной</span>
                </button>
                <button @click="create.modal = true" class="btn-indigo">
                    <span>Добавить</span>
                    <span class="hidden md:inline">&nbsp;Товар</span>
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto bg-white rounded shadow">
            <table class="w-full whitespace-nowrap">
                <tr class="font-bold text-left">
                    <th class="px-6 pt-6 pb-4">Название</th>
                    <th class="px-6 pt-6 pb-4">Количество</th>
                    <th class="px-6 pt-6 pb-4">Цена</th>
                    <th class="px-6 pt-6 pb-4">Сумма</th>
                </tr>
                <tr v-for="item in invoice.items" :key="item.id">
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4 focus:text-indigo-500">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            <input type="number" :value="item.count" @input="countHandler(item.id)" :id="`item_${item.id}_count`" class="w-full form-input" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            <input type="number" :value="item.price" @input="priceHandler(item.id)" :id="`item_${item.id}_price`" class="w-full form-input" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4">
                            {{ item.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice.items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Товары не найдены.</td>
                </tr>
            </table>
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
                <form @submit.prevent="store">
                    <input class="relative w-full px-4 py-2 border-b focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Поиск…" v-model="search" />
                    <div class="h-64 my-5 overflow-y-scroll">
                        <table class="w-full">
                            <tr class="font-bold text-left">
                                <th class="p-2 px-4">Название</th>
                                <th class="p-2 px-4 text-right">Измерения</th>
                            </tr>
                            <tr v-for="item in items" :key="item.id" class="hover:bg-gray-100 focus:bg-gray-100">
                                <td class="border-t">
                                    <div class="flex items-center px-4 py-2">
                                        {{ item.name }}
                                    </div>
                                </td>
                                <td class="border-t text-end">
                                    <div class="flex items-center px-4 py-2">
                                        {{ item.measurement }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td class="px-4 py-2 border-t" colspan="2">Товары не найдены.</td>
                            </tr>
                        </table>
                    </div>
                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Добавить Товар</loading-button>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script>
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
    },
    layout: Layout,
    props: {
        organization: Object,
        invoice: Object,
        items: Object,
    },
    remember: ['form', 'form_invoice'],
    data() {
        return {
            search: '',
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
                supplier: this.invoice.supplier,
                accepted: this.invoice.accepted,
                file: null,
            }),
        }
    },
    methods: {
        update() {
            this.form_invoice.post(`/organizations/${this.organization.id}/invoices/${this.invoice.id}`, {
                onSuccess: () => this.form.reset('file'),
            })
        },
        store() {
            this.form.post('', {
                onSuccess: () => {
                    this.form.reset('name')
                    this.form.reset('count')
                    this.form.reset('price')
                },
            })
        },
        countHandler(id) {
            clearTimeout(this.handler.count)
            this.handler.count = setTimeout(() => {
                let count = document.getElementById(`item_${id}_count`).value
                this.$inertia.put(`/invoices/${this.invoice.id}/invoice-items/${id}`, pickBy({ count: count }), { preserveState: true })
            }, 750)
        },
        priceHandler(id) {
            clearTimeout(this.handler.price)
            this.handler.price = setTimeout(() => {
                let price = document.getElementById(`item_${id}_price`).value
                this.$inertia.put(`/invoices/${this.invoice.id}/invoice-items/${id}`, pickBy({ price: price }), { preserveState: true })
            }, 750)
        },
    },
}
</script>
