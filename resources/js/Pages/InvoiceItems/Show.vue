<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="font-medium text-indigo-400">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ invoice.name }}
        </h1>
        <div class="w-full lg:flex">
            <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
                <form @submit.prevent="update">
                    <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                        <text-input disabled v-model="invoice.name" class="w-full pb-5 pr-6" label="Название" />
                        <text-input disabled v-model="invoice.supplier" class="w-full pb-5 pr-6 md:w-1/2" label="Поставщик" />
                        <text-input disabled v-model="invoice.accepted" class="w-full pb-5 pr-6 md:w-1/2" label="Принял" />
                        <text-input disabled v-model="invoice.date" class="w-full pb-5 pr-6 md:w-1/2" label="Дата" />
                    </div>
                </form>
            </div>
            <div v-if="invoice.file" class="w-full mt-4 break-words bg-white rounded-md shadow lg:ml-4 lg:mt-0 lg:w-1/3">
                <div class="flex flex-wrap p-8 -mb-8">
                    <div class="w-20 text-center">
                        <img src="/pdf.svg" class="w-full" alt="" />
                        <a :href="invoice.file" target="_blank" class="pt-2 hover:underline"> Файл </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="items-center justify-between mt-12 mb-6 lg:flex">
            <div class="w-full max-w-md mr-4">
                <h2 class="text-2xl font-bold">Товары</h2>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto text-sm bg-white rounded shadow">
            <table class="w-full whitespace-nowrap">
                <tr class="font-bold text-left">
                    <th class="px-6 pt-6 pb-4">Название</th>
                    <th class="px-6 pt-6 pb-4">Количество</th>
                    <th class="px-6 pt-6 pb-4">Цена</th>
                    <th class="px-6 pt-6 pb-4">Сумма</th>
                </tr>
                <tr v-for="item in invoice_items" :key="item.id">
                    <td class="border-t">
                        <div class="flex items-center px-6 py-4 focus:text-indigo-500">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-5 py-2">
                            {{ item.count }}
                            {{ item.measurement }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-5 py-2">
                            {{ item.price }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-5 py-2">
                            {{ (item.count * item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Товары не найдены.</td>
                </tr>
            </table>
        </div>
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
    },
}
</script>
