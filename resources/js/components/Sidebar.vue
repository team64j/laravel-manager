<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="$route" :loader-delay="1000" class="w-full h-full"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'Sidebar',
  components: { Layout },

  data () {
    return {
      data: null,
      meta: null,
      layout: null
    }
  },

  created () {
    this.get()
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    get () {
      axios.get('api/dashboard/sidebar').then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout
      })
    }
  }
}
</script>

<style scoped>

</style>

<style>
.app-sidebar > div {
  transform: translateZ(0) !important;
}
.app-sidebar .tab-row-container {
  @apply h-9 bg-cms-900
}
.app-sidebar .tab-row-container .prev, .app-sidebar .tab-row-container .next {
  @apply hidden
}
.app-sidebar .tab-row {
  @apply table w-full h-8 m-0 pt-0.5 border-spacing-x-1
}
.app-sidebar .tab-row .tab {
  @apply table-cell px-0.5 py-1 min-w-[1.5rem] h-8 rounded text-gray-400 bg-cms-700 hover:bg-cms-600
}
.app-sidebar .tab-row .tab.active {
  @apply text-white bg-blue-600 dark:bg-blue-600
}
</style>
