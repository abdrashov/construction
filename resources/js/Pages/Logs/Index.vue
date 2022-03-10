<template>
    <div>
        <Head title="Логи" />

        <h1 class="mb-6 text-2xl font-semibold">Логи</h1>

        <div class="overflow-x-auto text-sm bg-white shadow">
            <table class="w-full">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Пользователи</th>
                    <th class="px-4 py-3">Роль</th>
                    <th class="px-4 py-3">Действие</th>
                </tr>
                <tr v-for="log in logs.data" :key="log.id">
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2 font-medium">
                            {{ log.user }} 
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2">
                            {{ log.role }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="flex items-center px-4 py-2">
                            {{ log.description }}
                        </div>
                    </td>
                </tr>
                <tr v-if="logs.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">Логи не найдены.</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'

export default {
    components: {
        Head,
        Icon,
        Link,
        SearchFilter,
    },
    layout: Layout,
    props: {
        filters: Object,
        logs: Array,
    },
}
</script>
