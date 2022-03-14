<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1 ">
      <template v-for="(link, key) in links">
        <div v-if="link.url === null" :key="key" class="px-4 py-3 mb-1 mr-1 text-sm leading-4 bg-white border rounded hover:text-white focus:text-blue-400 hover:bg-blue-400 focus:border-blue-400" v-html="link.label" :class="{ 'bg-blue-400 text-white': link.active }" />
        <Link v-else :key="`link-${key}`" class="px-4 py-3 mb-1 mr-1 text-sm leading-4 bg-white border rounded hover:text-white focus:text-blue-400 hover:bg-blue-400 focus:border-blue-400" :class="{ 'bg-blue-400 text-white': link.active }" :href="link.url" v-html="link.label" />
      </template>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
  components: {
    Link,
  },
  props: {
    links: Array,
  },
}
</script>
