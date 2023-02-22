<template>
  <div class="app">
    <notifications position="top right" class="notifications"/>
    <window-component ref="window" @action="action"/>
    <div class="app-header">
      <menu-component ref="menu" @action="action"/>
    </div>
    <div class="app-body" @touchstart="touchstart">
      <div ref="mask" class="app-mask"></div>
      <div class="app-sidebar" :style="`width: ${x}px`">
        <sidebar ref="sidebar" @action="action"/>
      </div>
      <div class="app-separator" @mousedown="resizeStart"/>
      <div class="app-main">
        <global-tabs ref="globalTabs"/>
      </div>
    </div>
  </div>
</template>

<script>
import MenuComponent from '@/components/Menu/Menu.vue'
import Sidebar from '@/components/Sidebar.vue'
import GlobalTabs from '@/components/GlobalTabs'
import WindowComponent from '@/components/Window/Window.vue'
import store from '@/store'

export default {
  name: 'App.vue',
  components: { MenuComponent, Sidebar, GlobalTabs, WindowComponent },

  data () {
    this.sidebarWidth = 300
    this.sidebarMinWidth = 0
    this.sidebarMaxWidth = 800
    this.resize = false

    return {
      x: !navigator.mobile && this.$store.getters['Storage/get']('sidebar')?.width || this.sidebarWidth,
      o: 100,
      m: 0,
      tX: 0,
      theme: parseInt(this.$store.getters['Storage/get']('theme') || 1),
      dark: false,
      pages: {}
    }
  },

  created () {
    if (this.theme) {
      document.documentElement.className = this.themeClass()
    }
  },

  mounted () {
    this.$store.dispatch('Storage/set', ['sidebar', { width: this.x }])

    if (this.$store.getters['Storage/get']('sidebar')?.hide) {
      //this.x = this.sidebarMinWidth
      this.$el.classList.add('sidebar-hidden')
    }

    /**
     * @see https://dennisreimann.de/articles/delegating-html-links-to-vue-router.html
     */
    window.addEventListener('click', event => {
      // ensure we use the link, in case the click has been received by a sub element
      let { target } = event
      while (target && target.tagName !== 'A') target = target.parentNode
      // handle only links that do not reference external resources
      if (target && target.matches('a:not([href*=\'://\'])') && target.href) {
        // some sanity checks taken from vue-router:
        // https://github.com/vuejs/vue-router/blob/dev/src/components/link.js#L106
        const { altKey, ctrlKey, metaKey, shiftKey, button, defaultPrevented } = event
        // don't handle with control keys
        if (metaKey || altKey || ctrlKey || shiftKey) return
        // don't handle when preventDefault called
        if (defaultPrevented) return
        // don't handle right clicks
        if (button !== undefined && button !== 0) return
        // don't handle if `target="_blank"`
        if (target && target.getAttribute) {
          const linkTarget = target.getAttribute('target')
          if (/\b_blank\b/i.test(linkTarget)) return
        }
        // don't handle same page links/anchors
        const url = new URL(target.href)
        const to = url.pathname.replace(document.baseURI.replace(location.origin, ''), '/')
        if (window.location.pathname !== to && event.preventDefault) {
          event.preventDefault()
          this.$router.push(to)
        }
      }
    })
  },

  methods: {
    lang: (key) => store.getters['Lang/get'](key),
    config: (key) => store.getters['Config/get'](key),
    hasPermissions: (key) => store.getters['Auth/hasPermissions'](key),

    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    toggleSidebar () {
      if (this.$el.classList.contains('sidebar-hidden')) {
        this.$el.classList.remove('sidebar-hidden')
        this.$store.dispatch('Storage/set', ['sidebar', { hide: false }])
      } else {
        this.$el.classList.add('sidebar-hidden')
        this.$store.dispatch('Storage/set', ['sidebar', { hide: true }])
      }
    },

    touchstart (event) {
      const touchEvent = event.touches[0]
      const hidden = this.$el.classList.contains('sidebar-hidden')

      this.$el.classList.add('sidebar-resize')
      this.swipe = null
      this.resize = true
      this.startX = hidden ? this.$refs.sidebar.$el.offsetWidth : 0
      this.touchX = touchEvent.clientX
      this.touchY = touchEvent.clientY
      this.sidebarWidth = this.$refs.sidebar.$el.offsetWidth
      this.$refs.mask.style.opacity = hidden ? '0' : '100%'

      if (
          (this.touchX < 100 && hidden) ||
          (!this.$el.classList.contains('sidebar-hidden'))
      ) {
        event.currentTarget.addEventListener('touchmove', this.touchmove)
        event.currentTarget.addEventListener('touchend', this.touchend)
      }
    },

    touchmove (event) {
      const touchEvent = event.touches[0]
      const x = Math.abs(this.touchX - touchEvent.clientX)
      const y = Math.abs(this.touchY - touchEvent.clientY)

      if (x > y) {
        this.resize = true
        this.m = touchEvent.clientX - this.touchX - this.startX

        if (this.m > 0) {
          this.m = 0
        } else if (this.m < -this.sidebarWidth) {
          this.m = -this.sidebarWidth
        }

        this.o = (100 - (Math.abs(this.m) / (this.sidebarWidth / 100))).toFixed(2)
        this.swipe = this.o < 50

        this.$refs.sidebar.$el.parentElement.style.transform = 'translate3d(' + this.m + 'px, 0, 0)'
        this.$refs.mask.style.opacity = this.o + '%'
        this.$refs.mask.style.transition = 'none'
        this.$refs.mask.style.visibility = 'visible'
        this.$refs.mask.style.WebkitTransition = 'none'
        this.$refs.mask.style.WebkitTransition = 'none'
      } else {
        this.resize = false
        this.swipe = null
      }
    },

    touchend (event) {
      if (!this.resize) {
        //return
      }

      this.$el.classList.remove('sidebar-resize')

      if (this.swipe !== null) {
        if (this.swipe) {
          this.$el.classList.add('sidebar-hidden')
          this.$store.dispatch('Storage/set', ['sidebar', { hide: true }])
        } else {
          this.$el.classList.remove('sidebar-hidden')
          this.$store.dispatch('Storage/set', ['sidebar', { hide: false }])
        }
      }

      this.$refs.sidebar.$el.parentElement.style.transform = null
      this.$refs.mask.style.opacity = null
      this.$refs.mask.style.transition = null
      this.$refs.mask.style.visibility = null
      this.$refs.mask.style.WebkitTransition = null
      this.$refs.mask.style.WebkitTransition = null

      event.currentTarget.removeEventListener('touchmove', this.touchmove)
      event.currentTarget.removeEventListener('touchend', this.touchend)
    },

    resizeStart (event) {
      this.resize = event.buttons === 1

      if (this.resize) {
        this.$el.classList.add('sidebar-resize')
        document.onselectstart = () => false

        this.startX = event.clientX
        this.startW = this.$refs.sidebar.$el.offsetWidth
        this.$el.addEventListener('mousemove', this.resizeMove)
        this.$el.addEventListener('mouseup', this.resizeEnd)
      }
    },

    resizeMove (event) {
      if (!this.resize) {
        return
      }

      this.x = this.startW - (this.startX - event.clientX)
      if (this.x < this.sidebarMinWidth) {
        this.x = this.sidebarMinWidth
        this.resizeEnd()
      } else if (this.x > this.sidebarMaxWidth) {
        this.x = this.sidebarMaxWidth
        this.resizeEnd()
      } else if (this.$el.classList.contains('sidebar-hidden')) {
        this.$store.dispatch('Storage/set', ['sidebar', { hide: false }])
        this.$el.classList.remove('sidebar-hidden')
      }
    },

    resizeEnd () {
      if (!this.resize) {
        return
      }

      this.$el.classList.remove('sidebar-resize')
      document.onselectstart = () => null

      if (this.x === this.sidebarMinWidth) {
        this.$store.dispatch('Storage/set', ['sidebar', { hide: true, width: this.sidebarWidth }])
        this.$el.classList.add('sidebar-hidden')
      } else {
        this.$store.dispatch('Storage/set', ['sidebar', { hide: false, width: this.x }])
        this.$el.classList.remove('sidebar-hidden')
      }

      this.$el.removeEventListener('mousemove', this.resizeMove)
      this.$el.removeEventListener('mouseup', this.resizeEnd)
    },

    themeClass () {
      this.dark = this.theme !== 1
      return this.dark ? 'dark' : 'light'
    },

    toggleTheme (item) {
      for (const i in item.icons.theme) {
        if (item.icons.theme[i].key === this.theme) {
          const value = item.icons.theme?.[parseInt(i) + 1] || item.icons.theme[0]
          this.theme = value.key
          item.icon = value.value
          document.documentElement.className = this.themeClass()
          this.$store.dispatch('Storage/set', { theme: this.theme })
          break
        }
      }
    },

    scrollTo (to) {
      this.$refs.globalTabs.$refs.panel.scrollTop = parseInt(to)
    },

    pushRouter (route, callback) {
      if (typeof route === 'string') {
        route = this.$router.resolve(route)
      }

      this.$router.push(route).then(callback)
    }
  }
}
</script>

<style scoped>
.app {
  @apply flex flex-wrap flex-col h-full w-full bg-slate-100 dark:bg-cms-800
}
.app-header {
  @apply z-[60] grow-0 shrink-0 w-full bg-cms-800 text-gray-300
}
.app-body {
  @apply relative grow flex flex-nowrap w-full h-0
}
.app-mask {
  @apply invisible opacity-0 absolute z-[51] left-0 top-0 right-0 bottom-0 bg-black/40 md:bg-black/20 transition-all
}
.app:not(.sidebar-hidden) .app-mask {
  @apply visible opacity-100 md:invisible md:opacity-0
}
.app-header.active ~ .app-body > .app-mask {
  @apply visible opacity-100
}
.app-sidebar {
  @apply flex grow-0 shrink-0 absolute md:relative w-96 max-w-full md:min-w-0 h-full bg-cms-800 text-gray-100 z-[51] md:z-50 transition-all
}
.app.sidebar-resize .app-sidebar {
  @apply transition-none
}
.app:not(.sidebar-hidden) .app-sidebar {
  @apply translate-x-0
}
.sidebar-hidden .app-sidebar {
  @apply -translate-x-full md:translate-x-0 md:!w-0
}
.app-separator {
  @apply relative grow-0 shrink-0 hidden md:block w-1 bg-cms-600 cursor-e-resize hover:bg-blue-600 hover:opacity-100 z-20
}
.app.sidebar-resize .app-separator, .sidebar-hidden .app-sidebar:hover ~ .app-separator {
  @apply md:opacity-100 md:bg-blue-600
}
.app-separator::before {
  @apply absolute left-0 top-0 h-full w-3 -ml-1;
  content: "";
}
.app-main {
  @apply flex flex-col grow shrink-0 basis-0 overflow-hidden
}
</style>
