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
                            <date-picker v-model="form.date" mode="date" is24hr :masks="{input: 'DD.MM.YYYY'}">
                                <template v-slot="{ inputValue, inputEvents }">
                                    <input class="form-input" :class="{ error: form.errors.date }" :value="inputValue" v-on="inputEvents" />
                                </template>
                            </date-picker>
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
import { Calendar, DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'

export default {
    components: {
        Head,
        Link,
        LoadingButton,
        SelectInput,
        TextInput,
        FileInput,
        Calendar,
        DatePicker,
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
                date: ref(new Date()),
                supplier_id: null,
                accepted: this.organization.users ? this.organization.users[0].lastname + ' ' + this.organization.users[0].firstname : null,
                file: null,
            }),
        }
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
