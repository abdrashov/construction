<template>
    <Head title="Login" />
    <div class="flex items-center min-h-screen p-6 bg-gray-50">
        <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl">
            <div class="flex flex-col overflow-y-auto md:flex-row">
                <div class="h-32 md:w-1/2 md:h-auto">
                    <img aria-hidden="true" class="object-cover w-full h-full" src="/img/building.jfif" alt="Office" />
                </div>
                <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                    <form class="w-full" @submit.prevent="login">
                        <h1 class="mb-4 text-xl font-semibold text-gray-700">Войти</h1>

                        <text-input v-model="form.login" :error="form.errors.login" label="Логин" type="text" placeholder="builder" autofocus autocapitalize="off" />

                        <text-input v-model="form.password" :error="form.errors.password" class="mt-4" label="Пароль" placeholder="***************" type="password" />

                        <loading-button :loading="form.processing" class="w-full mt-4 btn-indigo" type="submit">Войти</loading-button>

                        <hr class="my-8" />

                        <label class="flex items-center mt-4 select-none" for="remember">
                            <input id="remember" v-model="form.remember" class="mr-2" type="checkbox" />
                            <span class="text-sm font-medium text-indigo-600 hover:underline">Запомнить Меня</span>
                        </label>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    components: {
        Head,
        LoadingButton,
        Logo,
        TextInput,
    },
    data() {
        return {
            form: this.$inertia.form({
                login: 'example@example.com',
                password: 'password',
                remember: false,
            }),
        }
    },
    methods: {
        login() {
            this.form.post('/login')
        },
    },
}
</script>
