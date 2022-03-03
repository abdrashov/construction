<template>
    <div>
        <Head :title="form.name" />
        <h1 class="mb-6 text-2xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Объекты</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта объект была удалена. </trashed-message>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-4 pr-6 w-full text-sm" label="Название" />
                    <text-input v-model="form.address" :error="form.errors.address" class="pb-4 pr-6 w-full text-sm" label="Адресс" />
                </div>
                <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                    <button v-if="!organization.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Удалить Объект</button>
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Обновить объект</loading-button>
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
        organization: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: this.organization.name,
                address: this.organization.address,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/organizations/${this.organization.id}`)
        },
        destroy() {
            this.$swal({
                title: 'Вы уверены, что хотите удалить эту объект?',
                text: 'После того, как он будет сохранен, отредактировать его будет невозможно!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#19ab4f',
                cancelButtonColor: '#838383',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(`/organizations/${this.organization.id}`)
                }
            })
        },
        restore() {
            if (confirm('Вы уверены, что хотите восстановить эту объект?')) {
                this.$inertia.put(`/organizations/${this.organization.id}/restore`)
            }
        },
    },
}
</script>
