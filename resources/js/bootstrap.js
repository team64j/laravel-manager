import _ from 'lodash'
import axios from 'axios'
import { notify } from '@kyvg/vue3-notification'
import store from '@/store'
import router from '@/router'

window._ = _

navigator.mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)

if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
  document.documentElement.classList.add('ios')
}

if (navigator.mobile) {
  document.documentElement.classList.add('mobile')
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Authorization'] = 'Bearer ' + (localStorage['token'] || '')
axios.defaults.baseURL = document.baseURI

// Add a request interceptor
axios.interceptors.request.use(function (config) {
  return config
}, function (error) {
  return Promise.reject(error)
})

// Add a response interceptor
axios.interceptors.response.use(function (response) {
  return response
}, function (error) {
  const status = error.response?.status || 500

  if (status === 401) {
    location.href = document.baseURI + '/logout'
  }

  if (error.response?.data?.message) {
    store.dispatch('GlobalTabs/del', router.currentRoute.value).then(() => notify({
      text: error.response.data.message,
      type: 'error'
    }))
  }

  return Promise.reject(error)
})

window.axios = axios

/**
 * Auth
 */
const auth = async () => await store.dispatch('Auth/check')
await auth().then(r => console.log('%c Auth: ' + r, r ? 'color: seagreen' : 'color: crimson'))

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

// import Alpine from 'alpinejs'
// window.Alpine = Alpine
// Alpine.start()
