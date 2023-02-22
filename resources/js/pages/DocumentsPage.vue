<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'DocumentsPage',
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

  computed: {
    id () {
      return this.meta?.['id'] || this.currentRoute['params'] && this.currentRoute['params']['id'] || null
    },
    title () {
      return this.meta?.['pagetitle'] || (this.meta?.['id'] && '...') || this.$root.lang('new_resource')
    }
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
      meta: { title: '...' },
      changed: false,
      loading: true
    })

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
      this.data = null

      axios.get('api/documents/' + this.id, {
        params: this.currentRoute['query'] || {}
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          meta: { title: this.title },
          changed: false,
          loading: false
        })
      })
    },

    filter (str) {
      if (!str || str.length > 1) {
        this.$emit(
            'action',
            'pushRouter',
            str ? '?filter=' + str : '',
            () => this.get(this.element)
        )
      }
    },

    pagination (url) {
      this.$emit(
          'action',
          'pushRouter',
          '?' + url,
          () => this.get(this.element)
      )
    },

    sort (sort, order) {
      this.$emit(
          'action',
          'pushRouter',
          '?order=' + sort + '&dir=' + order,
          () => this.get(this.element)
      )
    }
  }
}
</script>

<style scoped>

</style>
