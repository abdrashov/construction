<template>
    <div>
        <Head title="Создать Объект" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="text-indigo-400 font-medium">/</span> Создать
        </h1>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input v-model="form.name" class="pb-8 pr-6 w-full" label="Название" />
                    <text-input v-model="form.address" class="pb-8 pr-6 w-full" label="Адрес" />
                    <label class="form-label">Зав складом:</label>
                    <div v-for="(user, index) in user_form" :key="index" class="flex">
                        <text-input v-model="user.lastname" class="pb-4 pr-4 w-full" placeholder="Фамилия" />
                        <text-input v-model="user.firstname" class="pb-4 pr-4 w-full" placeholder="Имя" />
                        <text-input v-model="user.middlename" class="pb-4 pr-4 w-full" placeholder="Отчество" />
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
                    <button @click.prevent="addUser()" class="mb-8 ml-auto mr-6 px-2 py-1 text-white whitespace-nowrap text-xs font-bold leading-4 bg-green-500 hover:bg-orange-400 focus:bg-orange-400 rounded">Добавить поля</button>
                </div>
                <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Объект</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import pickBy from 'lodash/pickBy'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    components: {
        Head,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
    },
    layout: Layout,
    remember: 'form',
    data() {
        return {
            form: {
                name: null,
                address: null,
            },
            user_form: [
                {
                    firstname: null,
                    lastname: null,
                    middlename: null,
                    default: true,
                },
            ],
        }
    },
    methods: {
        store() {
            this.$inertia.post(
                '/organizations',
                pickBy({
                    users: this.user_form,
                    ...this.form,
                }),
                { preserveState: true },
            )
        },
        addUser() {
            this.user_form.push({
                firstname: null,
                lastname: null,
                middlename: null,
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
