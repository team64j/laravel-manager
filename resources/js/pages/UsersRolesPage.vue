<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'UsersRolesPage',
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

      axios.get('api/userrole', {
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
    },

    sort (sort, order) {
      this.$emit(
          'action',
          'pushRouter',
          '?order=' + sort + '&dir=' + order,
          () => this.get()
      )
    }
  }
}
</script>

<style scoped>

</style>
