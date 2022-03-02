<template>
    <div>
        <Head :title="form.firstname" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/reference/accepted">Принимающие</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ form.firstname }} {{ form.lastname }}
        </h1>
        <trashed-message v-if="accepted.deleted_at" class="mb-6" @restore="restore"> Этот принимающи был удален. </trashed-message>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <text-input v-model="form.firstname" :error="form.errors.firstname" class="w-full pb-8 pr-6" label="Фамилия" />
                    <text-input v-model="form.lastname" :error="form.errors.lastname" class="w-full pb-8 pr-6" label="Имя" />
                    <text-input v-model="form.middlename" :error="form.errors.middlename" class="w-full pb-8 pr-6" label="Отчество" />
                </div>
                <div class="flex items-center px-8 py-4 border-t border-gray-100 bg-gray-50">
                    <button v-if="!accepted.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить</button>
                    <loading-button :loading="form.processing" class="ml-auto btn-indigo" type="submit">Обновить</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
    components: {
        Head,
        Icon,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        TrashedMessage,
    },
    layout: Layout,
    props: {
        accepted: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                firstname: this.accepted.firstname,
                lastname: this.accepted.lastname,
                middlename: this.accepted.middlename,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/reference/accepted/${this.accepted.id}`)
        },
        destroy() {
            if (confirm('Вы уверены, что хотите удалить?')) {
                this.$inertia.delete(`/reference/accepted/${this.accepted.id}`)
            }
        },
        restore() {
            if (confirm('Вы уверены, что хотите восстановить?')) {
                this.$inertia.put(`/reference/accepted/${this.accepted.id}/restore`)
            }
        },
    },
}
</script>
