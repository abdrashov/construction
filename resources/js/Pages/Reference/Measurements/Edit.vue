<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-semibold">
            <Link class="text-sky-500 hover:text-sky-700" href="/reference/measurements">Измерение</Link>
            <span class="font-medium text-sky-500">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="measurement.deleted_at" class="mb-6" @restore="restore"> Этот принимающи был удален. </trashed-message>
        <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
            <form @submit.prevent="update">
                <div class="flex flex-wrap px-4 py-3 ">
                    <text-input v-model="form.name" :error="form.errors.name" class="w-full pb-4" label="Название" />
                </div>
                <div class="flex items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <button v-if="!measurement.deleted_at" class="text-sm text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить</button>
                    <loading-button :loading="form.processing" class="ml-auto btn-green" type="submit">Обновить</loading-button>
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
        measurement: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: this.measurement.name,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/reference/measurements/${this.measurement.id}`)
        },
        destroy() {
            this.$swal({
                title: 'Вы уверены, что хотите удалить?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(`/reference/measurements/${this.measurement.id}`)
                }
            })
        },
        restore() {
            this.$swal({
                title: 'Вы уверены, что хотите восстановить?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, восстановить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.put(`/reference/measurements/${this.measurement.id}/restore`)
                }
            })
        },
    },
}
</script>
