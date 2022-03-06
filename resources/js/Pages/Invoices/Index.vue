<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="text-sky-500 font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта объект была удалена. </trashed-message>

        <div v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Администратор'" class="w-full bg-white shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="items-start lg:flex">
                    <div class="flex flex-wrap px-4 py-3 w-full lg:pr-0 lg:w-1/2">
                        <text-input v-model="form.name" class="pb-4 w-full text-sm" label="Название" />
                        <text-input v-model="form.address" class="pb-4 w-full text-sm" label="Адрес" />
                    </div>
                    <div class="flex flex-wrap px-4 py-3 w-full lg:w-1/2">
                        <label class="form-label text-sm">Зав склад:</label>
                        <div v-for="(user, index) in user_form" :key="index" class="flex w-full">
                            <text-input v-model="user.lastname" class="pb-2 pr-2 w-1/2 text-sm" placeholder="Фамилия" />
                            <text-input v-model="user.firstname" class="pb-2 pr-2 w-1/2 text-sm" placeholder="Имя" />
                            <button
                                v-if="index !== 0"
                                type="submit"
                                @click.prevent="deleteUser(index)"
                                class="focus:shadow-outline-gray flex items-center justify-end mb-4 px-2 py-2 text-gray-500 hover:text-red-400 text-sm font-medium leading-5 bg-gray-100 hover:bg-red-100 rounded-lg focus:outline-none duration-200"
                                aria-label="Delete"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div v-if="index === 0" class="focus:shadow-outline-gray flex items-center justify-end mb-4 px-2 py-2 text-yellow-500 text-sm font-medium leading-5 bg-yellow-100 rounded-lg focus:outline-none">
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="w-full text-right">
                            <button @click.prevent="addUser()" class="mr-6 px-2 py-1 text-white whitespace-nowrap text-xs font-semibold leading-4 bg-blue-400 hover:bg-orange-400 focus:bg-orange-400 rounded">Добавить поля</button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <button v-if="!organization.deleted_at" class="text-red-600 hover:underline text-sm" tabindex="-1" type="button" @click="destroy">Удалить Объект</button>
                    <loading-button :loading="form.processing" class="btn-green ml-auto" type="submit">Обновить Объект</loading-button>
                </div>
            </form>
        </div>

        <div class="mb-3 mt-6 text-xl md:flex md:items-center md:justify-between">
            <div class="mr-4">
                <h2 class="text-gray-600 font-semibold">Накладные</h2>
            </div>
            <div class="">
                <Link class="ml-3 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm mr-2" v-if="filters.name || filters.date || filters.supplier_id || filters.accepted || filters.status || filters.pay" :href="`/organizations/${this.organization.id}/invoices`">
                    Сброс
                </Link>
                <button @click="search.modal = true" class="btn-gray mr-2">
                    <span>Фильтр/Поиск</span>
                </button>
                <Link class="btn-blue" :href="`/organizations/${organization.id}/invoices/create`">
                    <span>Создать</span>
                    <span class="hidden md:inline">&nbsp;Накладной</span>
                </Link>
            </div>
        </div>

        <div class="text-sm bg-white shadow overflow-x-auto">
            <table class="w-full">
                <tr class="text-left text-gray-500 text-xs font-semibold tracking-wide bg-gray-50 border-b uppercase">
                    <th class="px-4 py-3">Номер</th>
                    <th class="px-4 py-3 border-l">Поставщик</th>
                    <th class="px-4 py-3 border-l">Принял</th>
                    <th class="px-4 py-3 border-l">Статусы</th>
                    <th colspan="2" class="px-4 py-3 border-l">Информация</th>
                </tr>
                <tr v-for="invoice in organization.invoices" :key="invoice.id">
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3 text-gray-900 font-medium" :href="`/invoices/${invoice.id}/invoice-items`">
                            {{ invoice.name }}
                            <icon v-if="invoice.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            {{ invoice.supplier }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            {{ invoice.accepted }}
                        </Link>
                    </td>
                    <td class="border-l border-t">
                        <div class="flex mx-4 text-xs">
                            <Link v-if="invoice.pay && invoice.status" class="px-2 py-1 whitespace-nowrap font-semibold leading-tight bg-gradient-to-r rounded-full from-green-100 to-blue-100" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                                <span class="text-green-700"> Оплачен </span>
                                /
                                <span class="text-blue-700"> Подвержен </span>
                            </Link>
                            <Link v-else-if="!invoice.pay && invoice.status" class="px-2 py-1 whitespace-nowrap font-semibold leading-tight bg-gradient-to-r rounded-full from-red-100 to-blue-100" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                                <span class="text-red-700"> Не оплачен </span>
                                /
                                <span class="text-blue-700"> Подвержен </span>
                            </Link>
                            <Link v-else-if="!invoice.pay && !invoice.status" class="px-2 py-1 whitespace-nowrap font-semibold leading-tight bg-gradient-to-r rounded-full from-red-100 to-gray-100" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                                <span class="text-red-700"> Не оплачен </span>
                                /
                                <span class="text-gray-700"> Не подтвержден </span>
                            </Link>
                        </div>
                    </td>
                    <td class="border-l border-t">
                        <Link class="flex items-center px-4 py-3" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            <div>
                                <div>{{ invoice.fullname }}</div>
                                <div class="text-gray-700 whitespace-nowrap text-xs font-medium">{{ invoice.date }}</div>
                            </div>
                        </Link>
                    </td>
                    <td class="w-16 border-l border-t">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link
                                class="focus:shadow-outline-gray flex items-center justify-end ml-2 px-2 py-2 text-gray-500 hover:text-orange-400 text-xs font-medium leading-5 bg-gray-100 hover:bg-orange-100 rounded-lg focus:outline-none duration-200"
                                :href="`/invoices/${invoice.id}/invoice-items`"
                            >
                                <icon name="right" class="w-4 h-4" />
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="organization.invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Накладные не найдены.</td>
                </tr>
            </table>
        </div>

        <ModalLeft @serach="serach" @close="search.modal = !search.modal" :isOpen="search.modal">
            <ul role="list" class="-my-6">
                <li class="pb-4">
                    <text-input v-model="search.name" class="w-full" label="Номер" />
                </li>
                <li class="pb-4">
                    <div class="w-full">
                        <label class="form-label">Дата:</label>
                        <date-picker v-model="search.date" mode="date" is24hr :masks="{ input: 'YYYY-MM-DD' }">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-input" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </date-picker>
                    </div>
                </li>
                <li class="pb-4">
                    <select-input v-model="search.supplier_id" class="w-full" label="Поставщик">
                        <option :value="null" />
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                    </select-input>
                </li>
                <li class="pb-4">
                    <select-input v-model="search.accepted" class="w-full" label="Принял">
                        <option :value="null" />
                        <option v-for="(user, index) in organization.users" :key="index" :value="user.lastname + ' ' + user.firstname">{{ user.lastname }} {{ user.firstname }}</option>
                    </select-input>
                </li>
                <li class="pb-4">
                    <select-input v-model="search.status" class="w-full" label="Статус">
                        <option :value="null" />
                        <option value="true">Подтвержден</option>
                        <option value="false">Не подтвержден</option>
                    </select-input>
                </li>
                <li>
                    <select-input v-model="search.pay" class="w-full" label="Оплата">
                        <option :value="null" />
                        <option value="true">Оплачен</option>
                        <option value="false">Не оплачен</option>
                    </select-input>
                </li>
            </ul>
        </ModalLeft>
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
        ModalLeft,
        DatePicker,
    },
    layout: Layout,
    props: {
        auth: Object,
        organization: Object,
        filters: Object,
        suppliers: Object,
    },
    remember: 'form',
    data() {
        return {
            form: {
                name: this.organization.name,
                address: this.organization.address,
            },
            user_form: this.organization.users,
            search: {
                modal: false,
                name: this.filters.name,
                date: this.filters.date,
                supplier_id: this.filters.supplier_id,
                accepted: this.filters.accepted,
                status: this.filters.status,
                pay: this.filters.pay,
            },
        }
    },
    methods: {
        serach() {
            this.$inertia.get(`/organizations/${this.organization.id}/invoices`, pickBy(this.search), { preserveState: true })
            this.search.modal = false
        },
        update() {
            this.$inertia.post(
                `/organizations/${this.organization.id}`,
                pickBy({
                    _method: 'put',
                    users: this.user_form,
                    ...this.form,
                }),
                { preserveState: true },
            )
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
