<template>
    <div>
        <Head title="Создать Пользователя" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/users">Пользователь</Link>
            <span class="text-indigo-400 font-medium">/</span> Создать
        </h1>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="flex flex-wrap px-4 py-3">
                    <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-4 w-full" label="Имя" />
                    <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-4 w-full" label="Фамилия" />
                    <text-input v-model="form.login" :error="form.errors.login" class="pb-4 w-full" label="Логин" />
                    <text-input v-model="form.password" :error="form.errors.password" class="pb-4 w-full" type="password" autocomplete="new-password" label="Password" />
                    <select-input v-model="form.role" :error="form.errors.role" class="pb-4 w-full" label="Роль">
                        <option value="Администратор">Администратор</option>
                        <option value="Бухгалтер">Бухгалтер</option>
                        <option value="Кассир">Кассир</option>
                    </select-input>
                </div>
                <div class="flex items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Пользователя</loading-button>
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
