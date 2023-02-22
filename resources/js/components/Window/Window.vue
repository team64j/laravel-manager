<template>
  <div v-if="active" class="window" :class="this.class">
    <div class="main" :style="{ width: width, height: height }">

      <div class="resizer resize-top"></div>
      <div class="resizer resize-right"></div>
      <div class="resizer resize-bottom"></div>
      <div class="resizer resize-left"></div>
      <div class="resizer resize-top-left"></div>
      <div class="resizer resize-top-right"></div>
      <div class="resizer resize-bottom-left"></div>
      <div class="resizer resize-bottom-right"></div>

      <div class="header">
        <div class="title">
          <i v-if="icon" :class="icon" class="mr-1"/>
          {{ title }}
        </div>
        <div class="actions">
          <i class="fa fa-close" @click="close"/>
        </div>
      </div>

      <div class="content">
        <component
            v-if="component || currentRoute"
            :is="getComponent()"
            :key="`Window-` + (currentRoute?.name ? currentRoute.name : component.name)"
            :data="data"
            :current-route="currentRoute"
            @action="action"/>
        <div v-else v-html="data"/>
      </div>

    </div>
  </div>
</template>

<script>
import { defineAsyncComponent, toRaw } from 'vue'

export default {
  name: 'WindowComponent',

  data () {
    return {
      active: false,
      data: null,
      resize: true,
      title: '',
      icon: 'far fa-folder-open',
      url: null,
      component: null,
      currentRoute: null,
      width: '85%',
      height: '85%'
    }
  },

  computed: {
    class () {
      let classNames = []

      if (this.active) {
        classNames.push('active')
      }

      if (this.resize) {
        classNames.push('resize')
      }

      return classNames.join(' ')
    }
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    open (params) {
      params = params ?? {}
      this.active = true

      for (const i in params) {
        this[i] = params[i]
      }
    },

    close () {
      this.active = false
      this.data = null
    },

    getComponent () {
      if (this.currentRoute && !this.component) {
        if (this.currentRoute?.meta?.title) {
          this.title = this.currentRoute.meta.title
        } else if (this.currentRoute?.meta?.lang) {
          this.title = this.$root.lang(this.currentRoute.meta.lang)
        }

        this.component = this.currentRoute.matched[0].components.default

        if (typeof this.currentRoute.matched[0].components.default === 'function') {
          this.component = defineAsyncComponent(this.component)
        }
      }

      return toRaw(this.component)
    },

    pushRouter (route) {
      this.currentRoute = this.$router.resolve(route)
    },

    panelSelectRow (data) {
      if (this?.context?.model !== undefined) {
        this.context.model = data
        this.close()
      }
    }
  }
}
</script>

<style scoped>
.window {
  @apply hidden fixed z-50 left-0 top-0 right-0 bottom-0 justify-center items-center bg-black/20;
  z-index: 999;
}
.window.active {
  @apply flex
}
.main {
  @apply rounded relative overflow-hidden flex flex-col z-10 min-w-[10rem] min-h-[10rem] bg-white dark:bg-cms-700 shadow-md
}
.resizer {
  @apply hidden absolute z-20 bg-gray-700
}
.window.resize .resizer {
  @apply block
}
.resize-top, .resize-bottom {
  @apply w-full z-40 cursor-n-resize
}
.resize-left, .resize-right {
  @apply h-full z-40 cursor-ew-resize
}
.resize-top-left, .resize-bottom-left, .resize-top-right, .resize-bottom-right {
  @apply z-50 cursor-ne-resize
}
.resize-top-left, .resize-bottom-right {
  @apply cursor-nw-resize
}
.resize-top, .resize-bottom, .resize-top-left, .resize-bottom-left, .resize-top-right, .resize-bottom-right {
  @apply h-1
}
.resize-left, .resize-right, .resize-top-left, .resize-bottom-left, .resize-top-right, .resize-bottom-right {
  @apply w-1
}
.resize-top, .resize-top-left, .resize-top-right {
  @apply top-0
}
.resize-bottom, .resize-bottom-left, .resize-bottom-right {
  @apply bottom-0
}
.resize-left, .resize-top-left, .resize-bottom-left {
  @apply left-0
}
.resize-right, .resize-top-right, .resize-bottom-right {
  @apply right-0
}
.header {
  @apply flex justify-between items-center flex-wrap h-8 grow-0 shrink-0 bg-slate-100 dark:bg-cms-600 border-b
}
.header .title {
  @apply px-3 py-1 grow
}
.header .actions {
  @apply flex h-full items-center px-1
}
.header .actions i {
  @apply inline-flex mx-0.5 w-6 h-full items-center justify-center cursor-pointer hover:text-rose-600
}
.content {
  @apply grow;
  max-height: calc(100% - 2rem);
}
.window.resize .content {
  @apply px-1 pb-1
}
</style>
