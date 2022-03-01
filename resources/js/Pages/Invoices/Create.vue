<template>
    <div>
        <Head title="Создать Накладной" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
             <span class="font-medium text-indigo-400">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}`"> {{ organization.name }} </Link>
            <span class="font-medium text-indigo-400">/</span> Создать
        </h1>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="store">
                <div class="flex flex-wrap p-8 -mb-8 -mr-6">
                    <text-input v-model="form.name" :error="form.errors.name" class="w-full pb-8 pr-6" label="Название" />
                    <text-input v-model="form.date" :error="form.errors.date" class="w-full pb-8 pr-6" label="Дата" />
                    <text-input v-model="form.supplier" :error="form.errors.supplier" class="w-full pb-8 pr-6" label="Поставщик" />
                    <text-input v-model="form.accepted" :error="form.errors.accepted" class="w-full pb-8 pr-6" label="Принял" />
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
            form: this.$inertia.form({
                name: null,
                date: null,
                supplier: null,
                accepted: null,
            }),
        }
    },
    props: {
        organization: Object,
    },
    methods: {
        store() {
            this.form.post('')
        },
    },
}
</script>
