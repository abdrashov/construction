<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта объект была удалена. </trashed-message>

        <div class="w-full overflow-hidden bg-white rounded-lg shadow">
            <form @submit.prevent="update">
                <div class="items-start lg:flex">
                    <div class="flex flex-wrap w-full px-4 py-3 lg:pr-0 lg:w-1/2">
                        <text-input v-model="form.name" class="w-full pb-4 text-sm" label="Название" />
                        <text-input v-model="form.address" class="w-full pb-4 text-sm" label="Адрес" />
                    </div>
                    <div class="flex flex-wrap w-full px-4 py-3 lg:w-1/2">
                        <label class="text-sm form-label">Зав склад:</label>
                        <div v-for="(user, index) in user_form" :key="index" class="flex w-full">
                            <text-input v-model="user.lastname" class="w-1/2 pb-2 pr-2 text-sm" placeholder="Фамилия" />
                            <text-input v-model="user.firstname" class="w-1/2 pb-2 pr-2 text-sm" placeholder="Имя" />
                            <button
                                v-if="index !== 0"
                                type="submit"
                                @click.prevent="deleteUser(index)"
                                class="flex items-center justify-end px-2 py-2 mb-4 text-sm font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-red-400 hover:bg-red-100 focus:outline-none"
                                aria-label="Delete"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div v-if="index === 0" class="flex items-center justify-end px-2 py-2 mb-4 text-sm font-medium leading-5 text-yellow-500 bg-yellow-100 rounded-lg focus:shadow-outline-gray focus:outline-none">
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="w-full text-right">
                            <button @click.prevent="addUser()" class="px-2 py-1 mr-6 text-xs font-semibold leading-4 text-white bg-blue-500 rounded whitespace-nowrap hover:bg-orange-400 focus:bg-orange-400">Добавить поля</button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <button v-if="!organization.deleted_at" class="text-sm text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить Объект</button>
                    <loading-button :loading="form.processing" class="ml-auto btn-indigo" type="submit">Обновить Объект</loading-button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mt-6 mb-3 text-xl">
            <div class="w-full max-w-md mr-4">
                <h2 class="font-semibold text-gray-600">Накладные</h2>
            </div>
            <Link class="btn-indigo" :href="`/organizations/${organization.id}/invoices/create`">
                <span>Создать</span>
                <span class="hidden md:inline">&nbsp;Накладной</span>
            </Link>
        </div>

        <div class="overflow-x-auto text-sm bg-white rounded-lg shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Название</th>
                    <th class="px-4 py-3 border-l">Поставщик</th>
                    <th class="px-4 py-3 border-l">Принял</th>
                    <th class="px-4 py-3 border-l">Статус</th>
                    <th colspan="2" class="px-4 py-3 border-l">Дата</th>
                </tr>
                <tr v-for="invoice in organization.invoices" :key="invoice.id">
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3 font-medium text-gray-900" :href="`/invoices/${invoice.id}/invoice-items`">
                            {{ invoice.name }}
                            <icon v-if="invoice.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 ml-2 fill-gray-400" />
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3 border-l" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            {{ invoice.supplier }}
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3 border-l" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            {{ invoice.accepted }}
                        </Link>
                    </td>
                    <td class="border-t border-l">
                        <Link class="mx-4 flex items-center text-xs" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            <span v-if="invoice.status" class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full whitespace-nowrap dark:text-green-100 dark:bg-green-700"> Подвержен </span>
                            <span v-else class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full whitespace-nowrap dark:text-gray-100 dark:bg-gray-700"> Не подтвержден </span>
                        </Link>
                    </td>
                    <td class="border-t">
                        <Link class="flex items-center px-4 py-3 border-l" :href="`/invoices/${invoice.id}/invoice-items`" tabindex="-1">
                            {{ invoice.date }}
                        </Link>
                    </td>
                    <td class="w-16 border-t border-l">
                        <div class="flex items-center justify-end px-4 py-1">
                            <Link
                                class="flex items-center justify-end px-2 py-2 ml-2 text-xs font-medium leading-5 text-gray-500 duration-200 bg-gray-100 rounded-lg focus:shadow-outline-gray hover:text-orange-400 hover:bg-orange-100 focus:outline-none"
                                :href="`/invoices/${invoice.id}/invoice-items`"
                            >
                                <icon name="right" class="w-4 h-4"/>
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-if="organization.invoices.length === 0">
                    <td class="px-6 py-4 border-t" colspan="6">Накладные не найдены.</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Modal from '@/Shared/Modal'
import pickBy from 'lodash/pickBy'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import FileInput from '@/Shared/FileInput'

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
    },
    remember: 'form',
    data() {
        return {
            form: {
                name: this.organization.name,
                address: this.organization.address,
            },
            user_form: this.organization.users,
        }
    },
    methods: {
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
