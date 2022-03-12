<template>
    <div>
        <Head title="Расходы" />
        <h1 class="mb-6 text-2xl font-semibold">
            Расходы
        </h1>

        <div class="flex items-center justify-end mb-3 mt-6 text-xl">
            <Link :href="`/expense-common/create`" class="btn-indigo mr-2">
                <span>Добавить</span>
                <span class="hidden md:inline">&nbsp;Расходы</span>
            </Link>
        </div>

        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-2 py-3 border-l">Наименование</th>
                    <th class="px-2 py-3 border-l">Категория</th>
                    <th class="px-2 py-3 border-l">Договарная сумма</th>
                    <th class="px-2 py-3 border-l">Оплаченная сумма</th>
                    <th class="px-2 py-3 border-l">Дата</th>
                    <th colspan="2" class="px-4 py-3 border-l">Информация</th>
                </tr>
                <tr v-for="(expense, index) in expenses" :key="expense.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-3 text-gray-900 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.name }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.category }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.price }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.paid }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex whitespace-nowrap items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.date }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-3 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.fullname }}
                        </Link>
                    </td>
                    <td class="w-16 border-l border-t">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link class="focus:shadow-outline-gray flex items-center justify-end ml-2 px-2 py-2 text-gray-500 hover:text-orange-400 text-xs font-medium leading-5 bg-gray-100 hover:bg-orange-100 rounded-lg focus:outline-none duration-200" :href="`/expense-common/${expense.id}`">
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="expenses.length !== 0">
                    <td class="px-4 py-4 font-semibold border-t" colspan="4">ИТОГО</td>
                    <td class="border-l border-t" colspan="4">
                        <div class="flex items-center px-4 whitespace-nowrap font-semibold">
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
