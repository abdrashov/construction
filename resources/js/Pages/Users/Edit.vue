<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <div class="flex justify-start max-w-3xl mb-8">
      <h1 class="text-3xl font-bold">
        {{ form.first_name }} {{ form.last_name }}
      </h1>
      <img v-if="user.photo" class="block w-8 h-8 ml-4 rounded-full" :src="user.photo" />
    </div>
    <div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
      <form @submit.prevent="update">
        <div class="flex flex-wrap p-8 -mb-8 -mr-6">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="w-full pb-8 pr-6 lg:w-1/2" label="Имя" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="w-full pb-8 pr-6 lg:w-1/2" label="Фамилия" />
          <text-input v-model="form.email" :error="form.errors.email" class="w-full pb-8 pr-6 lg:w-1/2" label="Почта" />
          <text-input v-model="form.password" :error="form.errors.password" class="w-full pb-8 pr-6 lg:w-1/2" type="password" autocomplete="new-password" label="Пароль" />
          <!-- <file-input v-model="form.photo" :error="form.errors.photo" class="w-full pb-8 pr-6 lg:w-1/2" type="file" accept="image/*" label="Photo" /> -->
        </div>
        <div class="flex items-center px-8 py-4 border-t border-gray-100 bg-gray-50">
          <loading-button :loading="form.processing" class="ml-auto btn-indigo" type="submit">Обновить</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    user: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/users/${this.user.id}`, {
        onSuccess: () => this.form.reset('password', 'photo'),
      })
    },
  },
}
</script>
