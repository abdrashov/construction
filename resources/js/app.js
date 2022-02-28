import { createApp, h } from 'vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue3'

import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas, far)

InertiaProgress.init()

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    title: (title) => (title ? `${title} - Строительная компания` : 'Строительная компания'),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .component('font-awesome-icon', FontAwesomeIcon)
            .use(plugin)
            .mount(el)
    },
})
