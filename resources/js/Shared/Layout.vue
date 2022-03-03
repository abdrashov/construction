<template>
    <div>
        <div id="dropdown" />
        <div class="md:flex md:flex-col">
            <div class="md:flex md:flex-col md:h-screen">
                <div class="md:flex md:flex-shrink-0">
                    <div class="flex items-center justify-between min-h-screen px-6 py-4 bg-white md:flex-shrink-0 md:justify-center md:w-64">
                        <Link class="mt-1" href="/">
                            <!-- <logo class="fill-white" width="120" height="28" /> -->
                            <div style="height: 28px"></div>
                        </Link>
                        <dropdown class="md:hidden" placement="bottom-end">
                            <template #default>
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
                            </template>
                            <template #dropdown>
                                <div class="w-64 mt-2 overflow-hidden bg-white rounded shadow-lg">
                                    <main-menu />
                                </div>
                            </template>
                        </dropdown>
                        <main-menu class="flex-shrink-0 hidden w-64 overflow-y-auto bg-white md:block" />
                    </div>
                    <div class="container px-4 px-6 py-8 mx-auto md:flex-1 md:pb-24 md:pt-6 md:px-6 md:overflow-y-auto" scroll-region>
                        <flash-messages />
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'

export default {
    components: {
        Dropdown,
        FlashMessages,
        Icon,
        Link,
        Logo,
        MainMenu,
    },
    props: {
        auth: Object,
    },
}
</script>
