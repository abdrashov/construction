<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="font-medium text-sky-500">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ invoice.name }}
        </h1>

        <div class="w-full overflow-hidden bg-white shadow">
            <div class="items-start lg:flex">
                <div class="flex flex-wrap w-full px-4 py-3">
                    <text-input disabled v-model="invoice.name" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/2" label="Название" />

                    <text-input disabled v-model="invoice.date" class="w-full pb-4 lg:w-1/2" label="Дата" />

                    <text-input disabled v-model="invoice.supplier" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Поставщик" />

                    <text-input disabled v-model="invoice.accepted" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Принял" />

                    <div v-if="invoice.file" class="flex items-center w-full py-2 pb-4 lg:w-1/3">
                        <a :href="invoice.file" target="_blank" class="block"> <img src="/img/pdf.svg" class="w-6" alt="" /> </a>
                        <a :href="invoice.file" target="_blank" class="block pl-2 hover:underline"> Сканер </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 mb-3 text-xl">
            <div class="w-full max-w-md mr-4">
                <h2 class="font-semibold text-gray-600">Товары</h2>
            </div>
        </div>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Название</th>
                    <th class="px-4 py-3 border-l">Количество</th>
                    <th class="px-4 py-3 border-l">Цена</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                </tr>
                <tr v-for="item in invoice_items" :key="item.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2 font-medium text-gray-900">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-2 font-medium text-gray-700 whitespace-nowrap">
                            {{ item.count }}
                            <span class="pl-1 font-semibold text-gray-400">
                                {{ item.measurement }}
                            </span>
                            <span class="hidden">{{ (sum.count += item.count) }}</span>
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-2 font-medium text-gray-700 whitespace-nowrap">
                            {{ item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                            <span class="hidden">{{ (sum.price += item.price) }}</span>
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-2 font-semibold text-indigo-800 whitespace-nowrap">
                            {{ (item.count * item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                            <span class="hidden">{{ (sum.sum += item.count * item.price) }}</span>
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Товары не найдены.</td>
                </tr>
                <tr v-else>
                    <td class="border-t"></td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 font-medium">
                            {{ sum.count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 font-medium">
                            {{ sum.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex px-4 py-2 font-semibold whitespace-nowrap">
                            {{ sum.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
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
    data() {
        return {
            sum: {
                price: 0,
                count: 0,
                sum: 0,
            },
        }
    },
}
</script>
