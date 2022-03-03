<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/reference/suppliers">Поставщики</Link>
            <span class="font-medium text-indigo-400">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="supplier.deleted_at" class="mb-6" @restore="restore"> Этот поставщик был удален. </trashed-message>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <text-input v-model="form.name" :error="form.errors.name" class="w-full pb-4 pr-6 text-sm" label="Фамилия" />
                </div>
                <div class="flex items-center px-8 py-4 border-t border-gray-100 bg-gray-50">
                    <button v-if="!supplier.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить</button>
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
        supplier: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: this.supplier.name,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/reference/suppliers/${this.supplier.id}`)
        },
        destroy() {
            if (confirm('Вы уверены, что хотите удалить?')) {
                this.$inertia.delete(`/reference/suppliers/${this.supplier.id}`)
            }
        },
        restore() {
            if (confirm('Вы уверены, что хотите восстановить?')) {
                this.$inertia.put(`/reference/suppliers/${this.supplier.id}/restore`)
            }
        },
    },
}
</script>
