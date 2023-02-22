<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'ConfigurationPage',
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

    cancel () {
      this.$emit('action', 'closeTab')
    },

    save () {
      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: false,
        loading: true
      })

      const data = []
      for (let i in this.data) {
        data.push({ setting_name: i, setting_value: this.data[i] })
      }

      axios.post('api/configuration', data).then(() => {
        this.$emit('action', 'setTab', { changed: false })
        this.$emit('action', 'closeTab', () => setTimeout(() => location.reload(), 0))
      })
    },

    get () {
      axios.get('api/configuration').then((r) => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          changed: false,
          loading: false
        })

        this.$watch(
            () => this.data,
            () => {
              this.$emit('action', 'setTab', {
                key: this._.vnode.key,
                changed: true
              })
            },
            {
              deep: true
            }
        )
      })
    }
  }
}
</script>

<style scoped>

</style>
