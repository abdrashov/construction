<template>
    <div>
        <Head :title="organization.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ organization.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта объект была удалена. </trashed-message>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <div class="pb-1 w-full text-gray-500 text-sm font-medium lg:w-1/3">Название:</div>
                    <div class="pb-5 w-full lg:w-2/3">
                        {{ organization.name }}
                    </div>
                    <div class="pb-8 w-full text-gray-500 text-sm font-medium lg:w-1/3">Адресс:</div>
                    <div class="pb-8 w-full lg:w-2/3">
                        {{ organization.address }}
                    </div>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mb-6 mt-12">
            <div class="mr-4 w-full max-w-md">
                <h2 class="text-2xl font-bold">Накладные</h2>
            </div>
            <button @click="create.modal = true" class="btn-indigo">
                <span>Создать</span>
                <span class="hidden md:inline">&nbsp;Накладной</span>
            </button>
        </div>

        <div class="mt-6 bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <tr class="text-left font-bold">
                    <th class="pb-4 pt-6 px-6">Название</th>
                    <th class="pb-4 pt-6 px-6">Поставщик</th>
                    <th class="pb-4 pt-6 px-6">Принял</th>
                    <th class="pb-4 pt-6 px-6">Статус</th>
                    <th class="pb-4 pt-6 px-6" colspan="2">Дата</th>
                </tr>
                <tr v-for="invoice in organization.invoices" :key="invoice.id">
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/contacts/${invoice.id}/edit`">
                            {{ invoice.name }}
                            <icon v-if="invoice.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${invoice.id}/edit`" tabindex="-1">
                            {{ invoice.supplier }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${invoice.id}/edit`" tabindex="-1">
                            {{ invoice.accepted }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${invoice.id}/edit`" tabindex="-1">
                            {{ invoice.status ? 'Подвержен' : 'Не подтвержден' }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4" :href="`/contacts/${invoice.id}/edit`" tabindex="-1">
                            {{ invoice.date }}
                        </Link>
                    </td>
                    <td class="w-px border-t">
                        <div class="flex pr-2">
                            <Link class="flex items-center justify-center w-7 h-7 bg-gray-100 hover:bg-orange-100 focus-within:bg-orange-100 rounded" :href="`/invoices/${invoice.id}/edit`" tabindex="-1">
                                <icon name="edit" class="block w-4 h-4 fill-gray-500" />
                            </Link>
                            <Link class="flex items-center justify-center ml-1 px-2 h-7 text-gray-500 text-xs bg-gray-100 hover:bg-orange-100 focus-within:bg-orange-100 rounded" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1"> Товары </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="organization.invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Накладные не найдены.</td>
                </tr>
            </table>
        </div>

        <Modal @close="create.modal = !create.modal" :isOpen="create.modal" :width="create.width">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="mw-auto flex flex-col pr-10 w-full text-left font-medium">Создать Накладной</div>
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
                    <text-input v-model="form.date" :error="form.errors.date" class="pb-5 w-full" label="Дата" />
                    <text-input v-model="form.supplier" :error="form.errors.supplier" class="pb-5 w-full" label="Поставщик" />
                    <text-input v-model="form.accepted" :error="form.errors.accepted" class="pb-5 w-full" label="Принял" />
                    <file-input v-model="form.file" :error="form.errors.file" class="pb-8 w-full" type="file" accept="file/*" label="Файл" />

                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Накладной</loading-button>
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
        organization: Object,
    },
    remember: 'form',
    data() {
        return {
            create: {
                modal: false,
            },
            form: this.$inertia.form({
                name: null,
                date: null,
                supplier: null,
                accepted: null,
                file: null,
            }),
        }
    },
    methods: {
        store() {
            this.form.post('', {
                onSuccess: () => {
                    this.form.reset('name')
                    this.form.reset('date')
                    this.form.reset('supplier')
                    this.form.reset('accepted')
                    this.form.reset('file')
                },
            })
        },
    },
}
</script>
