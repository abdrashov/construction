<template>
    <div>
        <Head title="Отчеты" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="`/reports?organization_id=${organization.id}`+(form.begin ? '&begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">Отчеты</Link>
            <span class="text-sky-500 font-medium">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/reports/${organization.id}/${supplier.id}/all?`+(form.begin ? 'begin='+form.begin : '')+(form.end ? '&end='+form.end : '')">
            {{ organization.name }}
            </Link>
            <span class="text-sky-500 font-medium">/</span>
            {{ invoice.name }}
        </h1>

        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-4 py-3 border-l">Название</th>
                    <th class="px-4 py-3 border-l">Количество</th>
                    <th class="px-4 py-3 border-l">Цена</th>
                    <th class="px-4 py-3 border-l">Сумма</th>
                </tr>
                <tr v-for="(item, index) in invoice_items" :key="item.id">
                    <td class="border-t w-12">
                        <div class="flex items-center px-4 py-1 text-gray-900 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 text-gray-900 font-medium">
                            {{ item.name }}
                            <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 text-gray-700 whitespace-nowrap font-medium">
                            {{ item.count }}
                            <span class="pl-1 text-gray-400 font-semibold">
                                {{ item.measurement }}
                            </span>
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 text-gray-700 whitespace-nowrap font-medium">
                            {{ item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-4 py-2 text-indigo-800 whitespace-nowrap font-semibold">
                            {{ (item.count * item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="invoice_items.length !== 0">
                    <td class="px-4 py-4 border-t font-semibold" colspan="4">ИТОГО</td>
                    <td class="border-t border-l ">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
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
