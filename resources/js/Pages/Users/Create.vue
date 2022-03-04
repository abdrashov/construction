<template>
    <div>
        <Head title="Создать Пользователя" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/users">Пользователь</Link>
            <span class="font-medium text-sky-500">/</span> Создать
        </h1>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="store">
                <div class="flex flex-wrap px-4 py-3">
                    <text-input v-model="form.first_name" :error="form.errors.first_name" class="w-full pb-4" label="Имя" />
                    <text-input v-model="form.last_name" :error="form.errors.last_name" class="w-full pb-4" label="Фамилия" />
                    <text-input v-model="form.login" :error="form.errors.login" class="w-full pb-4" label="Логин" />
                    <text-input v-model="form.password" :error="form.errors.password" class="w-full pb-4" type="password" autocomplete="new-password" label="Пароль" />
                    <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="w-full pb-4" type="password" label="Повторите пароль" />
                    <select-input v-model="form.role" :error="form.errors.role" class="w-full pb-4" label="Роль">
                        <option value="Администратор">Администратор</option>
                        <option value="Бухгалтер">Бухгалтер</option>
                        <option value="Кассир">Кассир</option>
                    </select-input>
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <loading-button :loading="form.processing" class="btn-blue" type="submit">Создать Пользователя</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import FileInput from '@/Shared/FileInput'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    components: {
        FileInput,
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
            form: this.$inertia.form({
                first_name: '',
                last_name: '',
                login: '',
                password: '',
                password_confirmation: '',
                role: 'Кассир',
            }),
        }
    },
    methods: {
        store() {
            this.form.post('/users')
        },
    },
}
</script>
