<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/reference/suppliers">Поставщики</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="supplier.deleted_at" class="mb-6" @restore="restore"> Этот поставщик был удален. </trashed-message>
        <div class="max-w-3xl bg-white rounded-lg shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap px-4 py-3 ">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-4 w-full" label="Фамилия" />
                </div>
                <div class="flex items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <button v-if="!supplier.deleted_at" class="text-red-600 hover:underline text-sm" tabindex="-1" type="button" @click="destroy">Удалить</button>
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
                    this.$inertia.delete(`/reference/suppliers/${this.supplier.id}`)
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
                    this.$inertia.put(`/reference/suppliers/${this.supplier.id}/restore`)
                }
            })
        },
    },
}
</script>
