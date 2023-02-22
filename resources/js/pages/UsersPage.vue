<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'UsersPage',
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
      meta: { title: this.title },
      changed: false,
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
      this.data = null

      axios.get('api/user/list', {
        params: this.currentRoute['query'] || null
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
            () => this.get()
        )
      }
    },

    pagination (url) {
      this.$emit(
          'action',
          'pushRouter',
          '?' + url,
          () => this.get()
      )
    },

    sort (sort, order) {
      this.$emit(
          'action',
          'pushRouter',
          '?order=' + sort + '&dir=' + order,
          () => this.get()
      )
    },

    filters (value, key) {
      const query = Object.assign({}, this.currentRoute['query'] || {})
      if (value !== '') {
        query[key] = value
      } else {
        delete query[key]
      }
      delete query['page']
      this.$emit(
          'action',
          'pushRouter',
          { query },
          () => this.get(this.element)
      )
    },
  }
}
</script>

<style scoped>

</style>
