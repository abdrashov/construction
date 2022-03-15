import { createApp, h } from 'vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

InertiaProgress.init()

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    title: (title) => (title ? `${title} - Строительная компания` : 'Строительная компания'),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(VueSweetalert2)
            .use(plugin)
            .mount(el)
    },
})
