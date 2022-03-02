<template>
    <div>
        <Head :title="organization.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ organization.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта объект была удалена. </trashed-message>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <div class="w-full pb-1 text-sm font-medium text-gray-500 lg:w-1/3">Название:</div>
                    <div class="w-full pb-5 lg:w-2/3">
                        {{ organization.name }}
                    </div>
                    <div class="w-full pb-8 text-sm font-medium text-gray-500 lg:w-1/3">Адресс:</div>
                    <div class="w-full pb-8 lg:w-2/3">
                        {{ organization.address }}
                    </div>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mt-12 mb-6">
            <div class="w-full max-w-md mr-4">
                <h2 class="text-2xl font-bold">Накладные</h2>
            </div>
            <Link class="btn-indigo" :href="`/organizations/${organization.id}/invoices/create`">
                <span>Создать</span>
                <span class="hidden md:inline">&nbsp;Накладной</span>
            </Link>
        </div>

        <div class="mt-6 overflow-x-auto bg-white rounded shadow">
            <table class="w-full">
                <tr class="font-bold text-left">
                    <th class="px-6 pt-6 pb-4">Название</th>
                    <th class="px-6 pt-6 pb-4">Поставщик</th>
                    <th class="px-6 pt-6 pb-4">Принял</th>
                    <th class="px-6 pt-6 pb-4">Статус</th>
                    <th class="px-6 pt-6 pb-4" colspan="2">Дата</th>
                </tr>
                <tr v-for="invoice in organization.invoices" :key="invoice.id">
                    <td class="border-t">
                        <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/contacts/${invoice.id}/edit`">
                            {{ invoice.name }}
                            <icon v-if="invoice.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
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
                            <Link class="flex items-center justify-center bg-gray-100 rounded w-7 h-7 hover:bg-orange-100 focus-within:bg-orange-100" :href="`/invoices/${invoice.id}/edit`" tabindex="-1">
                                <icon name="edit" class="block w-4 h-4 fill-gray-500" />
                            </Link>
                            <Link class="flex items-center justify-center px-2 ml-1 text-xs text-gray-500 bg-gray-100 rounded h-7 hover:bg-orange-100 focus-within:bg-orange-100" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1"> Товары </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="organization.invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Накладные не найдены.</td>
                </tr>
            </table>
        </div>
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
}
</script>
