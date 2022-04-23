<template>
    <div>
        <Head :title="invoice.name" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="text-sky-500 font-medium">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="text-sky-500 font-medium">/</span>
            {{ invoice.name }}
        </h1>
        
        <trashed-message v-if="invoice.deleted_at" class="mb-6" @restore="restore"> Эта накладной была удалена. </trashed-message>

        <div class="w-full bg-white shadow overflow-hidden">
            <div class="items-start lg:flex">
                <div class="flex flex-wrap px-4 py-3 w-full">
                    <text-input disabled v-model="invoice.name" class="pb-4 pr-0 w-full lg:pr-4 lg:w-1/2" label="Название" />

                    <text-input disabled v-model="invoice.date" class="pb-4 w-full lg:w-1/2" label="Дата" />

                    <text-input disabled v-model="invoice.supplier" class="pb-4 pr-0 w-full lg:pr-4 lg:w-1/3" label="Поставщик" />

                    <text-input disabled v-model="invoice.accepted" class="pb-4 pr-0 w-full lg:pr-4 lg:w-1/3" label="Принял" />

                    <div v-if="invoice.file" class="flex items-center pb-4 py-2 w-full lg:w-1/3">
                        <a :href="invoice.file" target="_blank" class="block"> <img src="/img/pdf.svg" class="w-6" alt="" /> </a>
                        <a :href="invoice.file" target="_blank" class="block pl-2 hover:underline"> Сканер </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-3 mt-6 text-xl">
            <div class="mr-4 w-full max-w-md">
                <h2 class="text-gray-600 font-semibold">Товары</h2>
            </div>
            <div class="lg:flex">
                <button v-if="!invoice.pay" @click="pay()" class="btn-cyan mb-1">
                    <span>Оплатить</span>
                </button>
            </div>
        </div>

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
                    <td class="pl-4 py-4 text-right border-t" colspan="4">Сумма</td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-1 w-full whitespace-nowrap font-medium">
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
        organization: Object,
        invoice: Object,
        invoice_items: Object,
        items: Object,
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
        restore() {
            this.$swal({
                title: 'Вы уверены, что хотите восстановить эту накладной?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, восстановить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.post(`/invoices/${this.invoice.id}/invoice-items/restore`)
                }
            })
        },
    },
}
</script>
