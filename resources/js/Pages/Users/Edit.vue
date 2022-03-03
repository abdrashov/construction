<template>
    <div>
        <Head :title="`${form.first_name} ${form.last_name}`" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/users">Пользователи</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ form.first_name }} {{ form.last_name }}
        </h1>
        <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore"> Этот пользователь был удален. </trashed-message>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap px-4 py-3">
                    <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-4 w-full" label="Имя" />
                    <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-4 w-full" label="Фамилия" />
                    <text-input v-model="form.login" :error="form.errors.login" class="pb-4 w-full" label="Логин" />
                    <text-input v-model="form.password" :error="form.errors.password" class="pb-4 w-full" type="password" autocomplete="new-password" label="Password" />
                    <select-input v-if="auth.user.id !== user.id" v-model="form.role" :error="form.errors.role" class="pb-4 w-full" label="Роль">
                        <option value="Администратор">Администратор</option>
                        <option value="Бухгалтер">Бухгалтер</option>
                        <option value="Кассир">Кассир</option>
                    </select-input>
                </div>
                <div class="flex items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <button v-if="!user.deleted_at && auth.user.id !== user.id" class="text-red-600 hover:underline text-sm" tabindex="-1" type="button" @click="destroy">Удалить Пользователя</button>
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Обновить пользователя</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
    components: {
        FileInput,
        Head,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        TrashedMessage,
    },
    layout: Layout,
    props: {
        user: Object,
        auth: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                _method: 'put',
                first_name: this.user.first_name,
                last_name: this.user.last_name,
                login: this.user.login,
                password: '',
                role: this.user.role,
            }),
        }
    },
    methods: {
        update() {
            this.form.post(`/users/${this.user.id}`)
        },
        destroy() {
            this.$swal({
                title: 'Вы уверены, что хотите удалить этого пользователя?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(`/users/${this.user.id}`)
                }
            })
        },
        restore() {
            this.$swal({
                title: 'Вы уверены, что хотите восстановить этого пользователя?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, восстановить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.put(`/users/${this.user.id}/restore`)
                }
            })
        },
    },
}
</script>
