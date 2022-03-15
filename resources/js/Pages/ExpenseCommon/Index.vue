<template>
    <div>
        <Head title="Расходы" />
        <h1 class="mb-6 text-2xl font-semibold">Расходы</h1>

        <div class="flex items-center justify-end mb-3 mt-6 text-xl">
            <button class="hidden mr-3 w-8 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm md:block" type="button" @click="reset">Сброс</button>
            <button @click="form.modal = true" class="btn-gray mt-2 md:mt-0">
                <span>Фильтр/Поиск</span>
            </button>
            <Link :href="`/expense-common/create`" class="btn-indigo md:ml-2">
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
                        <div class="flex items-center px-4 py-1 text-gray-900 font-medium">
                            {{ index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.name }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.category }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.price }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.paid }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900 whitespace-nowrap" :href="`/expense-common/${expense.id}`">
                            {{ expense.date }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-2 py-1 text-gray-900" :href="`/expense-common/${expense.id}`">
                            {{ expense.fullname }}
                        </Link>
                    </td>
                    <td class="w-10 border-l border-t">
                        <div class="flex items-center justify-end px-2 py-1">
                            <Link class="focus:shadow-outline-gray flex items-center justify-end px-1 py-1 text-gray-500 hover:text-orange-400 text-xs font-medium leading-5 bg-gray-100 hover:bg-orange-100 rounded-lg focus:outline-none duration-200" :href="`/expense-common/${expense.id}`">
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="expenses.length !== 0" class="bg-amber-200">
                    <td class="px-4 py-3 font-semibold border-t" colspan="4">ИТОГО</td>
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
        <ModalLeft @serach="getData" @close="form.modal = !form.modal" :isOpen="form.modal">
            <ul role="list" class="-my-6">
                <li class="pb-4">
                    <select-input v-model="form.expense_category_id" class="w-full" label="Категория">
                        <option :value="null"></option>
                        <option v-for="expense_category in expense_categories" :key="expense_category.id" :value="expense_category.id">{{ expense_category.name }}</option>
                    </select-input>
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата от:</label>
                        <date-picker v-model="form.begin" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата до:</label>
                        <date-picker v-model="form.end" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
            </ul>
        </ModalLeft>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import mapValues from 'lodash/mapValues'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import ModalLeft from '@/Shared/ModalLeft'
import pickBy from 'lodash/pickBy'
import { Calendar, DatePicker } from 'v-calendar'
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
        DatePicker,
        ModalLeft,
    },
    layout: Layout,
    props: {
        auth: Object,
        expenses: Object,
        filters: Object,
        paid_sum: Number,
        expense_categories: Object,
    },
    data() {
        return {
            form: {
                modal: false,
                begin: this.filters.begin,
                end: this.filters.end,
                expense_category_id: this.filters.expense_category_id,
            },
        }
    },
    methods: {
        reset() {
            this.form.begin = null
            this.form.end = null
            this.form.expense_category_id = null
            this.$inertia.get(`/expense-common`, pickBy(), { preserveState: true })
        },
        getData() {
            this.$inertia.get(`/expense-common`, pickBy(this.form), { preserveState: true })
            this.form.modal = false
        },
    },
}
</script>
