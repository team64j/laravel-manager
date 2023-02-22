<template>
  <div class="global-tabs">

    <div class="grow-0">
      <div class="tabs" ref="tabs">
        <div class="pane">
          <a v-for="(tab, i) in $store.getters['GlobalTabs/tabs']()"
             :key="i"
             :data-to="tab.path"
             :class="[tab.active ? 'active' : '', tab.meta.class]"
             :title="tab.meta.title"
             @mousedown="clickTab(tab)"
             @dblclick="dblClickTab(tab)">
            <span v-if="tab.loading || tab.meta.icon" class="icon">
              <loader-icon v-if="tab.loading"/>
              <i v-else-if="tab.meta.icon" :class="tab.meta.icon"/>
            </span>
            <span v-if="tab.meta.title" v-html="tab.meta.title" class="title"/>
            <i v-if="!tab.meta.fixed" class="fa fa-close close" @mousedown.stop="closeTab(tab)"/>
            <span v-if="tab['changed']" class="change">*</span>
          </a>
        </div>
      </div>
    </div>

    <div class="grow h-0 overflow-hidden">
      <div class="panel" ref="panel">

        <router-view v-slot="{ Component }">
          <keep-alive-component :include="$store.getters['GlobalTabs/keys']()">
            <component
                v-if="!route?.meta?.isIframe"
                :key="route?.meta?.group && route.name || route.path"
                :is="Component"
                :current-route="route"
                @action="action"/>
          </keep-alive-component>
        </router-view>

        <div
            v-for="{ path, matched: [{ components: { default: component }}] } in $store.getters['GlobalTabs/frames']()"
            v-show="route.path === path">
          <component
              :key="path"
              :is="getComponent(component)"
              :current-route="route"
              @action="action"/>
        </div>

      </div>
    </div>

  </div>
</template>

<script>
import KeepAliveComponent from './KeepAlive'
import { toRaw } from 'vue'

export default {
  name: 'GlobalTabsComponent',
  components: { KeepAliveComponent },

  watch: {
    $route (route) {
      this.route = this.$route
      this.addTab(route)
    }
  },

  data () {
    return {
      route: this.$route,
      closing: false
    }
  },

  created () {
    this.init()
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    getComponent (component) {
      return toRaw(component)
    },

    init () {
      this.$store.dispatch('GlobalTabs/init')
    },

    addTab (data) {
      this.$store.dispatch('GlobalTabs/add', data)
      this.$root.$refs.sidebar.selectParent = false
    },

    setTab (data) {
      this.$store.dispatch('GlobalTabs/set', data)
    },

    closeTab (callback) {
      const route = typeof callback === 'object' ? callback : this.route
      const tab = this.$store.getters['GlobalTabs/find'](route)
      if (tab?.changed && !confirm(this.$root.lang('warning_not_saved'))) {
        return
      }

      this.closing = true
      this.$store.dispatch('GlobalTabs/del', route).then(() => {
        setTimeout(() => this.closing = false, 100)

        if (typeof callback === 'function') {
          callback()
        }
      })
    },

    toTab (data) {
      const tab = this.$store.getters['GlobalTabs/find'](this.route)
      if (tab?.changed && !confirm(this.$root.lang('warning_not_saved'))) {
        return
      }
      this.$store.dispatch('GlobalTabs/to', data)
    },

    clickTab (tab) {
      if (this.closing) {
        return
      }
      this.$router.push(tab)
    },

    dblClickTab (tab) {
      if (this.closing) {
        return
      }
      this.$store.dispatch('GlobalTabs/reload', tab)
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
.global-tabs {
  @apply flex flex-col grow w-full
}
.tabs {
  @apply h-9 overflow-hidden relative bg-cms-900
}
.pane {
  @apply h-16 flex flex-nowrap overflow-auto relative z-[2] py-0.5
}
.pane a {
  @apply h-8 mx-0.5 rounded inline-flex justify-between items-center bg-cms-700 hover:bg-cms-600 text-gray-200 hover:text-white relative select-none cursor-pointer transition
}
.pane .active {
  @apply bg-blue-600 hover:bg-blue-600 text-white
}
.panel {
  @apply h-full overflow-auto
}
.panel > div {
  @apply min-h-full relative
}
.panel > div > iframe {
  @apply absolute left-0 top-0 right-0 bottom-0 w-full min-h-full overflow-auto border-none bg-slate-100 dark:bg-cms-800 dark:text-white
}
.icon {
  @apply grow-0 shrink-0 w-10 inline-flex items-center justify-center opacity-75 pointer-events-none
}
.title {
  @apply grow pl-3 w-32 pointer-events-none truncate
}
.icon + .title {
  @apply pl-0
}
.close {
  @apply inline-flex items-center px-2 h-full hover:text-red-500
}
.change {
  @apply absolute top-0 left-0 px-1 text-yellow-500 text-lg font-mono
}
.frames {
  @apply h-full w-full relative
}
</style>
