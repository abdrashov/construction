<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${invoice.organization_id}/invoices`"> Накладной </Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ invoice.name }}
        </h1>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Название:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ invoice.name }}
                    </div>
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Статус:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ invoice.status ? 'Подвержен' : 'Не подтвержден' }}
                    </div>
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Дата:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ invoice.date }}
                    </div>
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Поставшик:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ invoice.supplier }}
                    </div>
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Принял:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ invoice.accepted }}
                    </div>
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Файл:</div>
                    <div class="pb-8 w-full break-words lg:w-2/3">
                        {{ invoice.file }}
                    </div>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mb-6 mt-12">
            <div class="mr-4 w-full max-w-md">
                <h2 class="text-2xl font-bold">Товары</h2>
            </div>
            <button @click="create.modal = true" class="btn-indigo">
                <span>Добавить</span>
                <span class="hidden md:inline">&nbsp;Товар</span>
            </button>
        </div>

        <div class="mt-6 bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <tr class="text-left font-bold">
                    <th class="pb-4 pt-6 px-6">Название</th>
                    <th class="pb-4 pt-6 px-6">Количество</th>
                    <th class="pb-4 pt-6 px-6">Цена</th>
                    <th class="pb-4 pt-6 px-6" colspan="2">Сумма</th>
                </tr>
                <tr v-for="item in invoice.items" :key="item.id">
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/contacts/${item.id}/edit`">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${item.id}/edit`" tabindex="-1">
                            {{ item.count }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${item.id}/edit`" tabindex="-1">
                            {{ item.price }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${item.id}/edit`" tabindex="-1">
                            {{ item.sum }}
                        </Link>
                    </td>
                    <td class="w-px border-t">
                        <div class="flex pr-2">
                            <Link class="flex items-center justify-center w-7 h-7 bg-gray-100 hover:bg-indigo-100 focus-within:bg-indigo-100 rounded" :href="`/contacts/${invoice.id}/edit`" tabindex="-1">
                                <icon name="cheveron-right" class="block w-5 h-5 fill-gray-400" />
                            </Link>
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
                <form @submit.prevent="store">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-5 w-full" label="Название" />
                    <text-input v-model="form.count" :error="form.errors.count" class="pb-5 w-full" label="Количесто" />
                    <text-input v-model="form.price" :error="form.errors.price" class="pb-5 w-full" label="Цена" />

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
        invoice: Object,
    },
    remember: 'form',
    data() {
        return {
            create: {
                modal: false,
            },
            form: this.$inertia.form({
                name: null,
                count: null,
                price: null,
            }),
        }
    },
    methods: {
        store() {
            this.form.post('', {
                onSuccess: () => {
                    // this.form.reset('name')
                    // this.form.reset('count')
                    // this.form.reset('price')
                },
            })
        },
    },
}
</script>
