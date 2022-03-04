<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1 ">
      <template v-for="(link, key) in links">
        <div v-if="link.url === null" :key="key" class="px-4 py-3 mb-1 mr-1 text-sm leading-4 text-gray-500 border rounded" v-html="link.label" />
        <Link v-else :key="`link-${key}`" class="px-4 py-3 mb-1 mr-1 text-sm leading-4 bg-white border rounded focus:text-gray-500 hover:bg-gray-50 focus:border-gray-50"  :class="{ 'bg-gray-50': link.active }" :href="link.url" v-html="link.label" />
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
