<template>
    <div>
        <Head title="Оплата" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" :href="`/expense-common`">Расходы</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ expense.name }}
        </h1>

        <div class="w-full overflow-hidden bg-white shadow">
            <form @submit.prevent="update">
                <div class="items-start lg:flex">
                    <div class="flex flex-wrap w-full px-4 py-3">
                        <text-input v-model="form_update.name" :error="form_update.errors.name" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/2" label="Название" />
                        <div class="w-full pb-4 lg:w-1/2">
                            <label class="form-label">Дата:</label>
                            <date-picker v-model="form_update.date" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                                <template v-slot="{ inputValue, inputEvents }">
                                    <input class="form-input" :class="{ error: form_update.errors.date }" :value="inputValue" v-on="inputEvents" />
                                </template>
                            </date-picker>
                            <div v-if="form_update.errors.date" class="form-error">{{ form_update.errors.date }}</div>
                        </div>
                        <select-input v-model="form_update.expense_category_id" :error="form_update.errors.expense_category_id" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Категория">
                            <option :value="null"></option>
                            <option v-for="supplier in categories" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select-input>
                        <text-input v-maska="'#*.##'" v-model="form_update.price" :error="form_update.errors.price" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Договарная сумма" />
                        <text-input v-maska="'#*.##'" disabled v-model="expense.paid"  class="w-full pb-4 lg:w-1/3" label="Оплаченная сумма" />
                    </div>
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <loading-button :loading="form_update.processing" class="ml-auto btn-green" type="submit">Обновить Расход</loading-button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-end mt-6 mb-3 text-xl">
            <button @click="create.modal = true" class="mr-2 btn-blue">
                <span>Добавить</span>
                <span class="hidden md:inline">&nbsp;Оплату</span>
            </button>
        </div>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="w-12 px-4 py-3">#</th>
                    <th class="px-2 py-3 border-l">Сумма</th>
                    <th class="px-2 py-3 border-l">Дата</th>
                    <th class="px-2 py-3 border-l">Заметка</th>
                    <th colspan="2" class="px-4 py-3 border-l">Информация</th>
                </tr>
                <tr v-for="(history, index) in expenses_histories.data" :key="history.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-1 font-medium text-gray-900">
                            {{ (expenses_histories.current_page - 1) * expenses_histories.per_page + index + 1 }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-2 py-1 text-gray-900">
                            {{ history.price }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-2 py-1 text-gray-900 whitespace-nowrap">
                            {{ history.date }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-2 py-1 text-gray-900">
                            {{ history.name }}
                        </div>
                    </td>
                    <td class="border-t border-l">
                        <div class="flex items-center px-2 py-1 text-gray-900">
                            {{ history.fullname }}
                        </div>
                    </td>
                    <td class="w-10 border-t border-l">
                        <div class="flex items-center justify-end px-2 py-1">
                            <Link :href="`/expense-common/${expense.id}/${history.id}`" method="delete" as="button" class="flex items-center justify-end px-1 py-1 text-xs font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-red-400 hover:bg-red-100 focus:outline-none" >
                                <icon name="delete" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="expenses_histories.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="7">Оплаты не найдены.</td>
                </tr>
            </table>
        </div>
        <pagination class="mt-6" :links="expenses_histories.links" />
        <Modal @close="create.modal = !create.modal" :isOpen="create.modal">
            <div class="w-full">
                <div class="flex flex-row items-start mb-8 text-xl font-medium">
                    <div class="flex flex-col w-full pr-10 font-medium text-left mw-auto">Оплата</div>
                    <div class="flex items-center flex-shrink-0 ml-auto mr-0 space-x-4 font-bold text-indigo-900">
                        <button @click="create.modal = false" type="submit" class="p-2 text-gray-800 duration-200 bg-gray-100 rounded-full hover:text-gray-50 focus:text-gray-50 hover:bg-gray-500 focus:bg-gray-500">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form @submit.prevent="store">
                    <text-input v-maska="'#*.##'" v-model="form.price" :error="form.errors.price" class="w-full pb-5" label="Сумма" />
                    <div class="w-full pb-4">
                        <label class="form-label">Дата:</label>
                        <date-picker v-model="form.date" mode="date" is24hr :masks="{ input: 'DD.MM.YYYY' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :class="{ error: form.errors.date }" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                        <div v-if="form.errors.date" class="form-error">{{ form.errors.date }}</div>
                    </div>
                    <text-input v-model="form.name"  :error="form.errors.name" class="w-full pb-5" label="Заметка" />
                    <loading-button :loading="form.processing" class="btn-blue" type="submit">Оплатить</loading-button>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import ModalLeft from '@/Shared/ModalLeft'
import pickBy from 'lodash/pickBy'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import mapValues from 'lodash/mapValues'
import TrashedMessage from '@/Shared/TrashedMessage'
import Pagination from '@/Shared/Pagination'
import { Calendar, DatePicker } from 'v-calendar'
import Modal from '@/Shared/Modal'
import 'v-calendar/dist/style.css'
import { maska } from 'maska'

export default {
    directives: {
        maska,
    },
    components: {
        Head,
        Icon,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        TrashedMessage,
        ModalLeft,
        Modal,
        DatePicker,
        Pagination,
    },
    layout: Layout,
    props: {
        auth: Object,
        expenses_histories: Object,
        expense: Object,
        filters: Object,
        categories: Object,
    },
    remember: 'form',
    data() {
        return {
            create: {
                modal: false,
            },
            form: this.$inertia.form({
                name: null,
                price: null,
                date: null,
            }),
            form_update: this.$inertia.form({
                _method: 'PUT',
                name: this.expense.name,
                date: this.expense.date,
                expense_category_id: this.expense.expense_category_id,
                price: this.expense.price?.toString(),
            }),
        }
    },
    methods: {
        store() {
            this.form.post('', {
                onSuccess: () => {
                    this.form.reset('name')
                    this.form.reset('price')
                    this.form.reset('date')
                    this.create.modal = false
                },
            })
        },
        update() {
            this.form_update.post(`/expense-common/${this.expense.id}`, {
                onSuccess: () => {
                    this.form.reset('name')
                    this.form.reset('price')
                    this.form.reset('date')
                    this.create.modal = false
                },
            })
        },
    },
}
</script>
