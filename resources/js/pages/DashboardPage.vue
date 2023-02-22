<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'DashboardPage',
  components: { Layout },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null,
      meta: null,
      layout: null
    }
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
      meta: { title: '' },
      loading: true
    })

    this.get()
  },

  methods: {
    action () {
      typeof this[arguments[0]] === 'function' && this[arguments[0]](...Array.from(arguments).splice(1)) ||
      this.$emit('action', ...arguments)
    },

    get () {
      axios.get('api/dashboard', {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          meta: { title: '' },
          loading: false
        })
      })
    },

    pagination (url) {
      this.$emit(
          'action',
          'pushRouter',
          '?' + url,
          () => this.get(this.element)
      )
    }
  }
}
</script>

<style scoped>

</style>
