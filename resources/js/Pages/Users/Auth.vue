<template>
    <div>
        <Head :title="`${user.first_name} ${user.last_name}`" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/users">Пользователи</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ user.first_name }} {{ user.last_name }}
        </h1>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap px-4 py-3">
                    <text-input disabled v-model="user.first_name" class="w-full pb-4" label="Имя" />
                    <text-input disabled v-model="user.last_name" class="w-full pb-4" label="Фамилия" />
                    <text-input disabled v-model="user.login" class="w-full pb-4" label="Логин" />
                    <text-input v-model="form.old_password" :error="form.errors.old_password" class="w-full pb-4" type="password" autocomplete="new-password" label="Старый пароль" />
                    <text-input v-model="form.password" :error="form.errors.password" class="w-full pb-4" type="password" label="Новый пароль" />
                    <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="w-full pb-4" type="password" label="Повторите новый пароль" />
                    <select-input v-if="auth.user.id !== user.id" v-model="form.role" :error="form.errors.role" class="w-full pb-4" label="Роль">
                        <option value="Администратор">Администратор</option>
                        <option value="Бухгалтер">Бухгалтер</option>
                        <option value="Кассир">Кассир</option>
                    </select-input>
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                  <loading-button :loading="form.processing" class="ml-auto btn-green" type="submit">Обновить Пароль</loading-button>
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
                old_password: '',
                password: '',
                password_confirmation: '',
            }),
        }
    },
    methods: {
        update() {
            this.form.post(`/auth/${this.user.id}`, {
                onSuccess: () => {
                    this.form.reset('old_password')
                },
            })
        },
    },
}
</script>
