<template>
  <div>
    <Head title="Создать Организацию" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Организации</Link>
      <span class="text-indigo-400 font-medium">/</span> Создать
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full" label="Название" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full" label="Телефон" />
          <text-input v-model="form.address" :error="form.errors.address" class="pb-8 pr-6 w-full" label="Адрес" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать Организацию</loading-button>
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
        phone: null,
        address: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/organizations')
    },
  },
}
</script>
