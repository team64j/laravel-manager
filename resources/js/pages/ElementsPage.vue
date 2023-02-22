<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'ElementsPage',
  components: { Layout },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null,
      meta: null,
      layout: null,
      element: null
    }
  },

  watch: {
    'currentRoute.params.element' (element) {
      element && this.get(element)
    }
  },

  created () {
    this.get()
  },

  updated () {
    if (this.element !== this.currentRoute['params'].element) {
      this.get()
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

    get (element, params) {
      this.element = element || this.currentRoute['params'].element

      // this.data = {
      //   default: {
      //     filter: this.data?.default?.filter
      //   }
      // }

      this.data = null

      axios.get('api/elements/' + this.element, {
        params: params || this.currentRoute['query'] || {}
      }).then(({ data }) => {
        this.data = data.data
        this.layout = data.layout
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
