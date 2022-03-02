<template>
    <div>
        <div class="relative">
            <Link href="/" :class="(isUrl('') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="ml-4">Главная</span>
            </Link>
        </div>

        <div class="relative">
            <p class="w-full px-6 pt-6 text-xs font-medium text-gray-300 uppercase">Основные</p>
        </div>

        <div class="relative">
            <Link href="/organizations" :class="(isUrl('organizations') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <icon name="office" class="w-4 h-4 fill-white" />
                <span class="ml-4">Объекты</span>
            </Link>
        </div>
        <div class="relative">
            <Link href="/reports" :class="(isUrl('reports') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <icon name="printer" class="w-4 h-4 fill-white" />
                <span class="ml-4">Отчеты</span>
            </Link>
        </div>

        <div class="relative">
            <p class="w-full px-6 pt-6 text-xs font-medium text-gray-300 uppercase">Дополнительные</p>
        </div>

        <div class="relative">
            <Link href="/reference/suppliers" :class="(isUrl('reference/suppliers') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="ml-4">Поставщики</span>
            </Link>
        </div>
        <div class="relative">
            <Link href="/reference/accepted" :class="(isUrl('reference/accepted') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="ml-4">Принимающие</span>
            </Link>
        </div>
        <div class="relative">
            <Link href="/reference/items" :class="(isUrl('reference/items') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="ml-4">Товары</span>
            </Link>
        </div>
        <div class="relative">
            <Link href="/reference/measurements" :class="(isUrl('reference/measurements') ? 'bg-indigo-800' : 'hover:bg-indigo-800') + ' inline-flex items-center px-6 py-4 w-full text-white text-sm font-semibold transition-colors duration-150'">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="ml-4">Измерение</span>
            </Link>
        </div>
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
