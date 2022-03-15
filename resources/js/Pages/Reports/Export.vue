<template>
    <div>
        <Head :title="title" />
        <div contenteditable v-html="reports"></div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        Link,
    },
    props: {
        title: String,
        reports: String,
    },
}
</script>
