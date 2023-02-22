<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'TvPage',
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
      return this.currentRoute['params'] && this.currentRoute['params']['id'] || null
    },
    title () {
      return this.data?.name || (this.id && '...') || this.$root.lang('new_tmplvars')
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
      axios.get('api/tv/' + this.id, {
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

    save (stay) {
      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: false
      })

      this.meta = null

      if (this.data.id) {
        axios.patch('api/tv/' + this.data.id, this.data).then((r) => this.afterSave(r, stay))
      } else {
        axios.post('api/tv', this.data).then((r) => this.afterSave(r, stay))
      }
    },

    afterSave (response, stay) {
      this.data = response.data.data
      this.meta = response.data.meta
      this.layout = response.data.layout

      this.$nextTick(() => {
        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          changed: false
        })

        this.$store.dispatch('set', { stay: stay, saving: false })
      })

      switch (stay) {
        case 0:
          this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'tvs' } })
          break

        case 1:
          this.$emit('action', 'toTab', { name: 'Tv', params: { id: null } })
          break
      }
    },

    cancel () {
      this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'tvs' } })
    },

    'update:selectAdd' (value) {
      this.data.newcategory = value
    }
  }
}
</script>

<style scoped>

</style>
