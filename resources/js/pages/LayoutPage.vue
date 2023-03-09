<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'LayoutPage',
  components: { Layout },

  props: {
    currentRoute: Object
  },

  computed: {
    id () {
      return this.data?.['id'] || this.currentRoute['params'] && this.currentRoute['params']['id'] || null
    },
    title () {
      return this.data?.['pagetitle'] || this.data?.['title'] || this.data?.['name']
    }
  },

  data () {
    this.url = this.$route?.['meta']?.url

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

    get () {
      axios.get(this.url).then((r) => {
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
    },

    cancel () {
      this.$emit('action', 'closeTab')
    },

    delete () {
      if (confirm('OK ?')) {

      }
    },

    save (stay) {
      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: false,
        loading: true
      })

      if (this.id) {
        axios.patch(this.url + '/' + this.id, this.data).then(r => this.afterSave(r, stay))
      } else {
        axios.post(this.url, this.data).then(r => this.afterSave(r, stay))
      }
    },

    afterSave (response, stay) {
      if (response.data?.data?.['redirect']) {
        location.href = response.data.data['redirect']
      }

      if (response.data?.data['reload']) {
        location.reload()
      }

      this.data = response.data.data
      this.meta = response.data.meta
      this.layout = response.data.layout

      this.$nextTick(() => this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: false,
        loading: false
      }))

      switch (stay) {
        case 0:
          this.$emit('action', 'closeTab')
          break

        case 1:
          this.$emit('action', 'toTab', { name: this.$route['name'], params: { id: null } })
          break

        default:
          this.$nextTick(() => this.$emit('action', 'setTab', {
            key: this._.vnode.key,
            meta: { title: this.title }
          }))
      }
    }
  }
}
</script>

<style scoped>

</style>
