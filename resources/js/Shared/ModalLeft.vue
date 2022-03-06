<template>
    <transition leave-active-class="duration-75">
        <div v-show="isOpen" class="fixed inset-0 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-show="isOpen" @click="$emit('close')" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                </transition>
                <div class="fixed inset-y-0 right-0 flex pl-10 max-w-full pointer-events-none">
                    <transition enter-active-class="transform transition ease-out duration-300" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in duration-200" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                        <div v-show="isOpen" class="w-screen max-w-md pointer-events-auto">
                            <div class="flex flex-col h-full bg-white shadow-xl overflow-y-scroll">
                                <div class="flex-1 px-4 py-6 overflow-y-auto sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-gray-900 text-lg font-medium" id="slide-over-title">Фильтр/Поиск</h2>
                                        <div class="flex items-center ml-3 h-7">
                                            <button @click="$emit('close')" type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500">
                                                <span class="sr-only">Закрыть</span>
                                                <!-- Heroicon name: outline/x -->
                                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-8">
                                        <div class="flow-root">
                                            <slot></slot>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-6 border-t border-gray-200 sm:px-6">
                                    <div class="mt-6">
                                        <button @click="$emit('serach')" class="flex items-center justify-center px-6 py-3 w-full text-white text-base font-medium bg-sky-500 hover:bg-sky-600 border border-transparent rounded-md shadow-sm">Поиск</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    props: {
        isOpen: {
            type: Boolean,
            default: false,
            required: true,
        },
    },
}
</script>
