<template>
    <div>
        <Head title="Создать Накладной" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/organizations">Объекты</Link>
            <span class="font-medium text-sky-500">/</span>
            <Link class="text-sky-500 hover:text-sky-700" :href="`/organizations/${organization.id}/invoices`">{{ organization.name }}</Link>
            <span class="font-medium text-sky-500">/</span> Создать
        </h1>

        <div class="w-full overflow-hidden bg-white shadow">
            <form @submit.prevent="store">
                <div class="items-start lg:flex">
                    <div class="flex flex-wrap w-full px-4 py-3">
                        <text-input v-model="form.name" :error="form.errors.name" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/2" label="Номер" />

                        <div class="w-full pb-4 lg:w-1/2">
                            <label class="form-label">Дата:</label>
                            <Datepicker v-model="form.date" :format="date_format" locale="ru" cancelText="Отмена" selectText="Выбрать"></Datepicker>
                            <div v-if="form.errors.date" class="form-error">{{ form.errors.date }}</div>
                        </div>

                        <select-input v-model="form.supplier_id" :error="form.errors.supplier_id" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Поставщик">
                            <option :value="null"></option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select-input>

                        <select-input v-model="form.accepted" :error="form.errors.accepted" class="w-full pb-4 pr-0 lg:pr-4 lg:w-1/3" label="Принял">
                            <option v-for="(user, index) in organization.users" :key="index" :value="user.lastname + ' ' + user.firstname">{{ user.lastname }} {{ user.firstname }}</option>
                        </select-input>

                        <file-input v-model="form.file" :error="form.errors.file" class="w-full pb-4 lg:w-1/3" type="file" accept="file/*" label="Сканер" />

                    </div>
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <loading-button :loading="form.processing" class="ml-auto btn-blue" type="submit">Создать Накладной</loading-button>
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
        suppliers: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                date: ref(new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getDate()),
                supplier_id: null,
                accepted: this.organization.users ? this.organization.users[0].lastname + ' ' + this.organization.users[0].firstname : null,
                file: null,
            }),
        }
    },
    methods: {
        store() {
            if (this.form.date.toString().length > 10) {
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
