<template>
    <div>
        <ul>
            <li class="relative px-5 py-2">
                <span v-if="isUrl('')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="ml-4">Главная</span>
                </Link>
            </li>
            <li class="relative px-5 py-2">
                <span v-if="isUrl(`auth`)" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link :href="`/auth/${auth.user.id}`" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="ml-4">Мой Профиль</span>
                </Link>
            </li>
            <li class="relative px-5 py-2">
                <span v-if="isUrl('logout')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/logout" method="delete" as="button" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="ml-4">Выйти из системы</span>
                </Link>
            </li>
            <li class="relative px-5 pt-2">
                <p class="w-full text-xs font-medium text-gray-500 uppercase">Основные</p>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('organizations', 'invoices')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/organizations" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path opacity="0.3" d="M5 8.04999L11.8 11.95V19.85L5 15.85V8.04999Z" />
                        <path
                            d="M20.1 6.65L12.3 2.15C12 1.95 11.6 1.95 11.3 2.15L3.5 6.65C3.2 6.85 3 7.15 3 7.45V16.45C3 16.75 3.2 17.15 3.5 17.25L11.3 21.75C11.5 21.85 11.6 21.85 11.8 21.85C12 21.85 12.1 21.85 12.3 21.75L20.1 17.25C20.4 17.05 20.6 16.75 20.6 16.45V7.45C20.6 7.15 20.4 6.75 20.1 6.65ZM5 15.85V7.95L11.8 4.05L18.6 7.95L11.8 11.95V19.85L5 15.85Z"
                        />
                    </svg>
                    <span class="ml-4">Объекты</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('expense-common')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/expense-common" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            opacity="0.5"
                            d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM15 17C15 16.4 14.6 16 14 16H8C7.4 16 7 16.4 7 17C7 17.6 7.4 18 8 18H14C14.6 18 15 17.6 15 17ZM17 12C17 11.4 16.6 11 16 11H8C7.4 11 7 11.4 7 12C7 12.6 7.4 13 8 13H16C16.6 13 17 12.6 17 12ZM17 7C17 6.4 16.6 6 16 6H8C7.4 6 7 6.4 7 7C7 7.6 7.4 8 8 8H16C16.6 8 17 7.6 17 7Z"
                        />
                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" />
                    </svg>
                    <span class="ml-4">Расходы</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Кассир'" class="relative px-5 py-2">
                <span v-if="isUrl('reports')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/reports" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    <span class="ml-4">Отчеты</span>
                </Link>
            </li>
            <li v-if="auth.user.role !== 'Кассир'" class="relative px-5 pt-2">
                <p class="w-full text-xs font-medium text-gray-500 uppercase">Дополнительные</p>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Администратор'" class="relative px-5 py-2">
                <span v-if="isUrl('users')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/users" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Пользователи</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('reference/suppliers')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/reference/suppliers" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Поставщики</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('reference/items')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/reference/items" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Товары</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('reference/item-categories')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/reference/item-categories" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Категория товаров</span>
                </Link>
            </li>
            <li v-if="auth.user.role === 'Супер Администратор' || auth.user.role === 'Бухгалтер'" class="relative px-5 py-2">
                <span v-if="isUrl('reference/measurements')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/reference/measurements" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Измерение</span>
                </Link>
            </li>
            <!-- <li v-if="auth.user.role === 'Супер Администратор'" class="relative px-5 py-2">
                <span v-if="isUrl('log')" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-sky-600" aria-hidden="true"></span>
                <Link href="/log" class="inline-flex items-center w-full text-sm font-semibold text-gray-700 transition-colors duration-150 hover:text-gray-600">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="ml-4">Логи</span>
                </Link>
            </li> -->
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
