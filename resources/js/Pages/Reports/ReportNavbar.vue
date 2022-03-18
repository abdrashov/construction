<template>
    <div class="mb-2">
        <Link href="/reports/common" :class="`${isUrl('reports/common') ? 'px-5 cursor-pointer py-3 text-sm font-semibold leading-4 text-white bg-blue-400 rounded whitespace-nowrap' : 'btn-white'} inline-block mt-2 md:mt-0`">
            <span>Общий</span>
        </Link>
        <Link href="/reports" :class="`${isUrl('reports') ? 'px-5 cursor-pointer py-3 text-sm font-semibold leading-4 text-white bg-blue-400 rounded whitespace-nowrap' : 'btn-white'} inline-block ml-0 mt-2 md:ml-2 md:mt-0`">
            <span>По поставщикам</span>
        </Link>
        <Link href="/reports/items" :class="`${isUrl('reports/items') ? 'px-5 cursor-pointer py-3 text-sm font-semibold leading-4 text-white bg-blue-400 rounded whitespace-nowrap' : 'btn-white'} inline-block ml-0 mt-2 md:ml-2 md:mt-0`">
            <span>По товарам</span>
        </Link>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        Link,
    },
    methods: {
        isUrl(...urls) {
            let currentUrl = this.$page.url.substr(1)
            let currentUrlFirst = currentUrl.split('?')[0]
            return urls.filter((url) => url === currentUrlFirst).length
        },
    },
}
</script>
