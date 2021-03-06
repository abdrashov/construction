<template>
    <div>
        <Head title="Отчеты по поставщикам" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="`/reports?organization_id=${organization.id}`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">Отчеты по поставщикам</Link>
            <span class="font-medium text-sky-500">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/reports/${organization.id}/${supplier.id}/${form.old}?`+(form.begin ? 'begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
            {{ organization.name }}
            </Link>
            <span class="font-medium text-sky-500">/</span>
            {{ invoice.name }}
        </h1>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <div class="flex items-center mb-1 mx-4 mt-2">
                <span class="font-medium title-font w-1/3 text-sm text-gray-500 tracking-wider">Объект:</span>
                <span class="font-medium title-font w-2/3 text-gray-900 text-base">{{ organization.name }}</span>
            </div>
            <div class="flex items-center mb-1 mx-4">
                <span class="font-medium title-font w-1/3 text-sm text-gray-500 tracking-wider">Поставщик:</span>
                <span class="font-medium title-font w-2/3 text-gray-900 text-base">{{ supplier.name }}</span>
            </div>
            <div class="flex items-center mb-2 mx-4">
                <span class="font-medium title-font w-1/3 text-sm text-gray-500 tracking-wider">Накладной:</span>
                <span class="font-medium title-font w-2/3 text-gray-900 text-base">{{ invoice.name }}</span>
            </div>
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-4 py-3 border-l">Название</th>
                    <th class="px-4 py-3 border-l">Количество</th>
                    <th class="px-4 py-3 border-l">Цена</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                </tr>
                <tr v-for="(item, index) in invoice_items" :key="item.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-700 whitespace-nowrap">
                            {{ item.count }}
                            <span class="pl-1 font-semibold text-gray-400">
                                {{ item.measurement }}
                            </span>
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-700 whitespace-nowrap">
                            {{ item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 py-1 font-semibold text-indigo-800 whitespace-nowrap">
                            {{ item.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="4">ИТОГО</td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ invoice.sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="5">Товары не найдены.</td>
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
        filters: Object,
        organization: Object,
        supplier: Object,
        invoice: Object,
        invoice_items: Object,
        items: Object,
    },
    data() {
        return {
            form: {
                begin: this.filters.begin,
                end: this.filters.end,
                old: this.filters.old,
            },
        }
    },
    methods: {
        pay() {
            this.$swal({
                title: 'Вы уверены что хотите оплатить?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, оплатить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.get(`/invoices/${this.invoice.id}/invoice-items/pay`)
                }
            })
        },
    },
}
</script>
