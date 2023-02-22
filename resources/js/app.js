import './bootstrap'
import { createApp } from 'vue'
import App from '@/App.vue'
import store from '@/store'
import router from '@/router'
import Notifications from '@kyvg/vue3-notification'
import HelpIcon from '@/components/Layout/HelpIcon'
import LoaderIcon from '@/components/Layout/LoaderIcon'
import Loader from '@/components/Layout/Loader.vue'
import RenderString from '@/components/Layout/RenderString.vue'

const app = createApp(App)

window.Vue = app

/**
 * Register Global Components
 */
const components = import.meta.glob('./staticComponents/**', { eager: true })
Object.entries(components).
  forEach(([path, { default: module }]) => {
    path = path.split('staticComponents/')[1]
    const name = path.replace(/\.\w+$/, '')
    app.component(name, module)
  })

app.component('HelpIcon', HelpIcon)
app.component('LoaderIcon', LoaderIcon)
app.component('Loader', Loader)
app.component('RenderString', RenderString)

app.use(Notifications)
app.use(store)
app.use(router)
app.mount('#app')

console.log('%c Welcome ' + store.getters['Config/get']('APP_NAME'), 'color: royalblue')
