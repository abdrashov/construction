<template>
    <div>
        <ul>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="ml-4">Главная</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl(`/users/${auth.user.id}/edit`)" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link :href="`/users/${auth.user.id}/edit`" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="ml-4">Мой Профиль</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('logout')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/logout" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="ml-4">Выйти из системы</span>
                </Link>
            </li>
        </ul>

        <div class="relative">
            <p class="w-full px-6 pt-6 text-xs font-medium text-gray-500 uppercase">Основные</p>
        </div>

        <ul>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('organizations', 'invoices')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/organizations" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="ml-4">Объекты</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('reports')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/reports" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    <span class="ml-4">Отчеты</span>
                </Link>
            </li>
        </ul>

        <div class="relative">
            <p class="w-full px-6 pt-6 text-xs font-medium text-gray-500 uppercase">Дополнительные</p>
        </div>

        <ul>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('reference/suppliers')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/reference/suppliers" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Поставщики</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('reference/accepted')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/reference/accepted" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Принимающие</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('reference/items')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/reference/items" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Товары</span>
                </Link>
            </li>
            <li class="relative px-6 py-3">
                <span v-if="isUrl('reference/measurements')" class="absolute inset-y-0 left-0 w-1 bg-indigo-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                <Link href="/reference/measurements" class="inline-flex items-center w-full text-sm font-semibold text-gray-900 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Измерение</span>
                </Link>
            </li>
        </ul>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'

export default {
    components: {
        Icon,
        Link,
    },
    data() {
        return {
            auth: this.$page.props.auth,
        }
    },
    methods: {
        isUrl(...urls) {
            let currentUrl = this.$page.url.substr(1)
            if (urls[0] === '') {
                return currentUrl === ''
            }
            return urls.filter((url) => currentUrl.startsWith(url)).length
        },
    },
}
</script>
