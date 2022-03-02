<template>
    <div>
        <Head title="Создать Накладной" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="text-indigo-400 font-medium">/</span>
            <Link class="text-indigo-400 hover:text-indigo-600" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="text-indigo-400 font-medium">/</span> Создать
        </h1>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-5 pr-6 w-full" label="Название" />
                    <select-input v-model="form.supplier_id" :error="form.errors.supplier_id" class="pb-5 pr-6 w-full md:w-1/2" label="Поставщик">
                        <option :value="null"></option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                    </select-input>
                    <select-input v-model="form.accepted_id" :error="form.errors.accepted_id" class="pb-5 pr-6 w-full md:w-1/2" label="Принял">
                        <option :value="null"></option>
                        <option v-for="accepted in accepteds" :key="accepted.id" :value="accepted.id">{{ accepted.lastname }} {{ accepted.firstname }}</option>
                    </select-input>
                    <div class="pb-5 pr-6 w-full md:w-1/2">
                        <label class="form-label">Дата:</label>
                        <Datepicker v-model="form.date" :format="date_format" locale="ru" cancelText="Отмена" selectText="Выбрать"></Datepicker>
                        <div v-if="form.errors.date" class="form-error">{{ form.errors.date }}</div>
                    </div>
                    <file-input v-model="form.file" :error="form.errors.file" class="pb-8 pr-6 w-full md:w-1/2" type="file" accept="file/*" label="Файл" />
                </div>
                <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
                    <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Накладной</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import FileInput from '@/Shared/FileInput'
import Datepicker from 'vue3-date-time-picker'
import 'vue3-date-time-picker/dist/main.css'

export default {
    components: {
        Head,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        FileInput,
        Datepicker,
    },
    layout: Layout,
    remember: 'form',
    props: {
        organization: Object,
        accepteds: Object,
        suppliers: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                date: ref(new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getDate()),
                supplier_id: null,
                accepted_id: null,
                file: null,
            }),
        }
    },
    methods: {
        store() {
            if (this.form.date.toString().length > 9) {
                const day = this.form.date.getDate()
                const month = this.form.date.getMonth()+1
                const year = this.form.date.getFullYear()

                this.form.date = `${year}-${month}-${day}`
            }

            this.form.post(`/organizations/${this.organization.id}/invoices`, {
                onSuccess: () => {
                    this.form.reset('file')
                },
            })
        },
    },
    setup() {
        const date_format = (date) => {
            const day = date.getDate()
            const month = date.getMonth() + 1
            const year = date.getFullYear()
            return `${year}-${month}-${day}`
        }
        return {
            date_format,
        }
    },
}
</script>
