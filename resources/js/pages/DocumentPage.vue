<template>
  <layout :data="data" :meta="meta" :layout="layout" @action="action" :current-route="currentRoute"/>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'DocumentPage',
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
      return this.data?.['id'] || this.currentRoute['params'] && this.currentRoute['params']['id'] || null
    },
    title () {
      return this.data?.['pagetitle'] || (this.data?.['id'] && '...') || this.$root.lang('new_resource')
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
      axios.get('api/document/' + this.id).then(r => {
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
        changed: false,
        loading: true
      })

      if (this.data.id) {
        axios.patch('api/document/' + this.data.id, this.data).then((r) => this.afterSave(r, stay))
      } else {
        axios.post('api/document', this.data).then((r) => this.afterSave(r, stay))
      }
    },

    afterSave (response, stay) {
      this.data = response.data.data
      this.meta = response.data.meta
      this.layout = response.data.layout

      this.$store.dispatch('set', { tree: { id: 'treeDocuments', action: 'updateNode', params: this.data } })
      this.$store.dispatch('set', { saving: false })

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
          this.$emit('action', 'toTab', { name: 'Document', params: { id: null } })
          break

        default:
          this.$nextTick(() => this.$emit('action', 'setTab', {
            key: this._.vnode.key,
            meta: { title: this.title }
          }))
      }
    },

    delete () {
      if (confirm('OK ?')) {

      }
    },

    cancel () {
      this.$emit('action', 'closeTab')
    },

    view () {
      window.open(this.meta.url, '_blank')
    },

    changeFormSelect (event, ctx) {
      console.log(ctx._.vnode.key)
    },

    'update:modelValue' (value, key, originValue, category, item) {
      //this.data[key] = value
      //console.log(arguments)
    },

    'mousedown:input' (event, ctx) {
      const target = event.target

      if ('parent' === ctx._.vnode.key) {
        this.selectParent(target, ctx)
      }
    },

    selectParent (target, ctx) {
      const msg = this.$root.lang('new_parent')

      if (!target.dataset.value) {
        target.dataset.value = target.value
      }

      if (target.value === msg) {
        this.setParent(target)
      } else {
        this.setParent(target, msg)

        if (!this.$store.getters['Config/get']('treeSelectNode')) {
          this.$store.dispatch('Config/set', { treeSelectNode: {} })
        }

        if (!this.uw) {
          this.uw = this.$watch(
              () => this.$store.state['Config'].treeSelectNode,
              async (node) => {
                this.$store.dispatch('Config/set', { treeSelectNode: false })
                this.uw()
                this.uw = null

                if (!node) {
                  await this.setParent(target)
                  return
                }

                node.loading = true
                target.disabled = true

                const parents = await axios.get('api/document/parents/' + node.id).then(r => r.data.data) || []

                if (this.data.id === node.id || this.data.id === node.parent ||
                    parents.some(i => i.id === this.data.id)) {
                  this.$notify({
                    text: this.$root.lang('unable_set_parent'),
                    type: 'error'
                  })
                  await this.setParent(target)
                } else if (node) {
                  ctx.model = node.id
                  await this.setParent(target, node['pagetitle'] + ' (' + node.id + ')')
                }

                node.loading = false
                target.disabled = false
              }
          )
        }
      }
    },

    setParent (target, value) {
      if (value) {
        this.$nextTick(() => target.value = value)
      } else {
        this.$nextTick(() => target.value = target.dataset.value)
      }
    }
  }
}
</script>

<style scoped>

</style>
