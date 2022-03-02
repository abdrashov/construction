<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/reference/items">Товары</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="item.deleted_at" class="mb-6" @restore="restore"> Этот товар был удален. </trashed-message>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full" label="Фамилия" />
                    <select-input v-model="form.measurement_id" :error="form.errors.measurement_id" class="pb-5 w-full" label="Измерение">
                        <option :value="null"></option>
                        <option v-for="measurement in measurements" :key="measurement.id" :value="measurement.id">{{ measurement.name }}</option>
                    </select-input>
                </div>
                <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                    <button v-if="!item.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить</button>
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Обновить</loading-button>
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
        item: Object,
        measurements: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: this.item.name,
                measurement_id: this.item.measurement_id,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/reference/items/${this.item.id}`)
        },
        destroy() {
            if (confirm('Вы уверены, что хотите удалить?')) {
                this.$inertia.delete(`/reference/items/${this.item.id}`)
            }
        },
        restore() {
            if (confirm('Вы уверены, что хотите восстановить?')) {
                this.$inertia.put(`/reference/items/${this.item.id}/restore`)
            }
        },
    },
}
</script>
