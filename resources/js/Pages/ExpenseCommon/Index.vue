<template>
    <div>
        <Head title="Расходы" />
        <h1 class="mb-6 text-2xl font-semibold">
            Расходы
        </h1>

        <div class="flex items-center justify-end mt-6 mb-3 text-xl">
            <Link :href="`/expense-common/create`" class="mr-2 btn-indigo">
                <span>Добавить</span>
                <span class="hidden md:inline">&nbsp;Расходы</span>
            </Link>
        </div>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-2 py-3 border-l">Наименование</th>
                    <th class="px-2 py-3 border-l">Категория</th>
                    <th class="px-2 py-3 border-l">Договарная сумма</th>
                    <th class="px-2 py-3 border-l">Оплаченная сумма</th>
                    <th class="px-2 py-3 border-l">Дата</th>
                    <th colspan="2" class="px-4 py-3 border-l">Информация</th>
                </tr>
                <tr v-for="(expense, index) in expenses" :key="expense.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.name }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.category }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.price }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.paid }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900 whitespace-nowrap" :href="`/expense-common/${expense.id}`">
                            {{ expense.date }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.fullname }}
                        </Link>
                    </td>
                    <td class="w-10 border-t border-l">
                        <div class="flex items-center justify-end px-2 py-1">
                            <Link class="flex items-center justify-end px-1 py-1 text-xs font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-orange-400 hover:bg-orange-100 focus:outline-none" :href="`/expense-common/${expense.id}`">
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="expenses.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t " colspan="4">ИТОГО</td>
                    <td class="border-t border-l" colspan="4">
                        <div class="flex items-center px-4 font-semibold whitespace-nowrap">
                            {{ paid_sum?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') }}
                        </div>
                    </td>
                </tr>
                <tr v-if="expenses.length === 0">
                    <td class="px-6 py-4 border-t" colspan="7">Расходы не найдены.</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import 'v-calendar/dist/style.css'

export default {
    components: {
        Head,
        Icon,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        TrashedMessage,
    },
    layout: Layout,
    props: {
        auth: Object,
        expenses: Object,
        filters: Object,
        paid_sum: Number,
    },
}
</script>
