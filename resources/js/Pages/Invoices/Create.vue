<template>
    <div>
        <Head title="Создать Накладной" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="font-medium text-indigo-400">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-indigo-400">/</span> Создать
        </h1>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="store">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <text-input v-model="form.name" :error="form.errors.name" class="w-full pb-5 pr-6" label="Название" />
                    <text-input v-model="form.supplier" :error="form.errors.supplier" class="w-full pb-5 pr-6 md:w-1/2" label="Поставщик" />
                    <text-input v-model="form.accepted" :error="form.errors.accepted" class="w-full pb-5 pr-6 md:w-1/2" label="Принял" />
                    <text-input v-model="form.date" :error="form.errors.date" class="w-full pb-5 pr-6 md:w-1/2" label="Дата" />
                    <file-input v-model="form.file" :error="form.errors.file" class="w-full pb-8 pr-6 md:w-1/2" type="file" accept="file/*" label="Файл" />

                </div>
                <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100 bg-gray-50">
                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Накладной</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import FileInput from '@/Shared/FileInput'

export default {
    components: {
        Head,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        FileInput
    },
    layout: Layout,
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                date: null,
                supplier: null,
                accepted: null,
                file: null,
            }),
        }
    },
    props: {
      organization: Object,
    },
    methods: {
        store() {
            this.form.post(`/organizations/${this.organization.id}/invoices`, {
                onSuccess: () => {
                    this.form.reset('file')
                },
            })
        },
    },
}
</script>
