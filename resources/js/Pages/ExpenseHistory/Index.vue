<template>
    <div>
        <Head :title="organization.name" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="text-sky-500 font-medium">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="text-sky-500 font-medium">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/expense`">{{ expense.name }}</Link>
            <span class="text-sky-500 font-medium">/</span>
            Оплата
        </h1>

        <div class="w-full bg-white shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="items-start lg:flex">
                    <div class="flex flex-wrap px-4 py-3 w-full">
                        <text-input v-model="form_update.name" :error="form_update.errors.name" class="pb-4 pr-0 w-full lg:pr-4 lg:w-1/2" label="Название" />
                        <div class="pb-4 w-full lg:w-1/2">
                            <label class="form-label">Дата:</label>
                            <date-picker v-model="form_update.date" mode="date" is24hr :masks="{ input: 'YYYY-MM-DD' }">
                                <template v-slot="{ inputValue, inputEvents }">
                                    <input class="form-input" :class="{ error: form_update.errors.date }" :value="inputValue" v-on="inputEvents" />
                                </template>
                            </date-picker>
                            <div v-if="form_update.errors.date" class="form-error">{{ form_update.errors.date }}</div>
                        </div>
                        <select-input v-model="form_update.expense_category_id" :error="form_update.errors.expense_category_id" class="pb-4 pr-0 w-full lg:pr-4 lg:w-1/3" label="Категория">
                            <option :value="null"></option>
                            <option v-for="supplier in categories" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select-input>
                        <text-input v-maska="'#*.##'" v-model="form_update.price"  :error="form_update.errors.price" class="pb-4  pr-0 w-full lg:pr-4 lg:w-1/3" label="Договарная сумма" />
                        <text-input v-maska="'#*.##'" disabled v-model="expense.paid"  class="pb-4 w-full lg:w-1/3" label="Оплаченная сумма" />
                    </div>
                </div>
                <div class="flex items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <loading-button :loading="form_update.processing" class="btn-green ml-auto" type="submit">Обновить Расход</loading-button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-end mb-3 mt-6 text-xl">
            <button @click="create.modal = true" class="btn-blue mr-2">
                <span>Добавить</span>
                <span class="hidden md:inline">&nbsp;Оплату</span>
            </button>
        </div>

        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-2 py-3 border-l">Сумма</th>
                    <th class="px-2 py-3 border-l">Дата</th>
                    <th class="px-2 py-3 border-l">Заметка</th>
                    <th colspan="2" class="px-4 py-3 border-l">Информация</th>
                </tr>
                <tr v-for="(history, index) in expenses_histories.data" :key="history.id">
                    <td class="w-12 border-t">
                        <div class="flex items-center px-4 py-3 text-gray-900 font-medium">
                            {{ (expenses_histories.current_page - 1) * expenses_histories.per_page + index + 1 }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-2 py-3 text-gray-900">
                            {{ history.price }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-2 py-3 text-gray-900">
                            {{ history.date }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-2 py-3 text-gray-900">
                            {{ history.name }}
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex items-center px-2 py-3 text-gray-900">
                            {{ history.fullname }}
                        </div>
                    </td>
                    <td class="w-16 border-l border-t">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link :href="`/organizations/expenses/${expense.id}/${history.id}`" method="delete" as="button" class="focus:shadow-outline-gray flex items-center justify-end ml-2 px-2 py-2 text-gray-500 hover:text-red-400 text-xs font-medium leading-5 bg-gray-100 hover:bg-red-100 rounded-lg focus:outline-none duration-200" >
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
                    <div class="mw-auto flex flex-col pr-10 w-full text-left font-medium">Оплата</div>
                    <div class="flex flex-shrink-0 items-center ml-auto mr-0 text-indigo-900 font-bold space-x-4">
                        <button @click="create.modal = false" type="submit" class="p-2 hover:text-gray-50 focus:text-gray-50 text-gray-800 bg-gray-100 hover:bg-gray-500 focus:bg-gray-500 rounded-full duration-200">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path opacity="0.4" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" />
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form @submit.prevent="store">
                    <text-input v-maska="'#*.##'" v-model="form.price" :error="form.errors.price" class="pb-5 w-full" label="Сумма" />
                    <div class="pb-4 w-full">
                        <label class="form-label">Дата:</label>
                        <date-picker v-model="form.date" mode="date" is24hr :masks="{ input: 'YYYY-MM-DD' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :class="{ error: form.errors.date }" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                        <div v-if="form.errors.date" class="form-error">{{ form.errors.date }}</div>
                    </div>
                    <text-input v-model="form.name"  :error="form.errors.name" class="pb-5 w-full" label="Заметка" />
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
        organization: Object,
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
        serach() {
            this.$inertia.get(`/organizations/${this.organization.id}/expenses_histories`, pickBy(this.search), { preserveState: true })
            this.search.modal = false
        },
        update() {
            this.form_update.post(`/organizations/${this.organization.id}/expense/${this.expense.id}`, {
                onSuccess: () => {
                    this.form.reset('name')
                    this.form.reset('price')
                    this.form.reset('date')
                    this.create.modal = false
                },
            })
        },
        destroy() {
            this.$swal({
                title: 'Вы уверены, что хотите удалить эту объект?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(`/organizations/${this.organization.id}`)
                }
            })
        },
        restore() {
            this.$swal({
                title: 'Вы уверены, что хотите восстановить эту объект?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, восстановить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.put(`/organizations/${this.organization.id}/restore`)
                }
            })
        },
        addUser() {
            this.user_form.push({
                firstname: null,
                lastname: null,
                default: false,
            })
        },
        deleteUser(index) {
            this.user_form = this.user_form.filter((value, key) => {
                return key !== index
            })
        },
    },
}
</script>
