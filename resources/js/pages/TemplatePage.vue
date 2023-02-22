<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'TemplatePage',
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
      return this.data?.['id'] || this.currentRoute['params'] && this.currentRoute?.['params']['id'] || null
    },
    title () {
      return this.data?.['templatename'] || (this.data?.['id'] && '...') || this.$root.lang('new_template')
    },
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
      meta: { title: '...' },
      changed: false
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

    'mousedown:select' (event, ctx) {
      // const key = ctx._.vnode.key
      //
      // if (key === 'category') {
      //   if (!ctx.url || event.target?.options.length > 1) {
      //     return
      //   }
      //
      //   axios.get(ctx.url, {
      //     params: {
      //       selected: event.target.value
      //     }
      //   }).then(r => ctx.options = r.data.data)
      // }
    },

    'update:modelValue' (value, key, originValue, category, item) {
      // if (this.data[key] !== undefined) {
      //   this.data[key] = value
      // }
      // if (key === 'tvs') {
      //   this.meta.tvs.data.map(c => {
      //     if (c.id === category) {
      //       c.data.map(i => {
      //         if (i.id === item) {
      //           i.attach = ~value.indexOf(originValue) ? 1 : 0
      //         }
      //       })
      //     }
      //   })
      // }
    },

    get () {
      axios.get('api/template/' + this.id, {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          meta: { title: this.title },
          changed: false
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
        axios.patch('api/template/' + this.data.id, this.data).then((r) => this.afterSave(r, stay))
      } else {
        axios.post('api/template', this.data).then((r) => this.afterSave(r, stay))
      }
    },

    afterSave (response, stay) {
      this.data = response.data.data
      this.meta = response.data.meta
      this.layout = response.data.layout

      this.$store.dispatch('set', { tree: { id: 'treeTemplates', action: 'updateNode', params: this.data } })

      this.$nextTick(() => {
        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          changed: false
        })

        this.$store.dispatch('set', { stay: stay, saving: false })
      })

      switch (stay) {
        case 0:
          this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'templates' } })
          break

        case 1:
          this.$emit('action', 'toTab', { name: 'Template', params: { id: null } })
          break
      }
    },

    cancel () {
      this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'templates' } })
    },

    filter (str) {
      if (!str || str.length > 1) {
        const route = this.$router.resolve(str ? '?filter=' + str : '')
        this.$router.push(route)

        this.meta = {
          tvs: {
            filter: this.meta?.tvs?.['filter']
          }
        }

        axios.get('api/template/' + this.id, {
          params: route.query || null
        }).then(r => {
          this.meta = r.data.meta
        })
      }
    },

    pagination (url) {
      const route = this.$router.resolve('?' + url)
      this.$router.push(route)

      this.meta = {
        tvs: {
          filter: this.meta?.tvs?.['filter']
        }
      }

      axios.get('api/template/' + this.id, {
        params: route.query || null
      }).then(r => {
        this.meta = r.data.meta
      })
    },

    sort (sort, order) {
      const route = this.$router.resolve('?order=' + sort + '&dir=' + order)
      this.$router.push(route)

      this.meta = {
        tvs: {
          filter: this.meta?.tvs?.['filter']
        }
      }

      axios.get('api/template/' + this.id, {
        params: route.query || null
      }).then(r => {
        this.meta = r.data.meta
      })
    },

    attach (item) {
      item.attach = item.attach ? 0 : 1

      const index = this.data.tvs.indexOf(item.id)

      if (item.attach) {
        !(index > -1) && this.data.tvs.push(item.id)
      } else {
        index > -1 && this.data.tvs.splice(index, 1)
      }
    }
  }
}
</script>

<style scoped>

</style>
