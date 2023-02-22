<template>
  <div class="flex flex-col">

    <actions-buttons :data="['clear']"/>

    <title-component/>

    <panel v-if="data" :data="data" @action="action" :current-route="currentRoute" class="py-4"/>

    <div v-else class="flex items-center justify-center grow">
      <loader/>
    </div>

  </div>
</template>

<script>
import ActionsButtons from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'SystemLogPage',
  components: { ActionsButtons, TitleComponent, Panel },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null,
      meta: null
    }
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
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
      if (this.data?.columns) {
        this.data.data = null
      }

      if (this.data?.pagination) {
        this.data.pagination = null
      }

      this.meta = null

      axios.get('api/systemlog', {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          loading: false
        })
      })
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

    pagination (url) {
      this.$emit(
          'action',
          'pushRouter',
          '?' + url,
          () => this.get(this.element)
      )
    },

    sort (sort, order) {
      const query = Object.assign({}, this.currentRoute['query'] || {})
      query['order'] = sort
      query['dir'] = order
      this.$emit(
          'action',
          'pushRouter',
          { query },
          () => this.get(this.element)
      )
    }
  }
}
</script>

<style scoped>

</style>
