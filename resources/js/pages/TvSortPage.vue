<template>
  <div>

    <actions-buttons :data="['save', 'cancel']" @action="action"/>

    <title-component/>

    <panel :data="data" class="py-4" @action="action" :current-route="currentRoute" @update:modelValue="updateModelValue"/>

  </div>
</template>

<script>
import ActionsButtons from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'TvSortPage',
  components: { ActionsButtons, TitleComponent, Panel },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null
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
      if (this.data?.columns) {
        this.data.data = null
      }

      if (this.data?.pagination) {
        this.data.pagination = null
      }

      axios.get('api/tv/sort', {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          changed: false,
          loading: false
        })
      })
    },

    pagination (url) {
      this.$emit(
          'action',
          'pushRouter',
          '?' + url,
          () => this.get(this.element)
      )
    },

    sortable (event, category) {
      category.data.map((i, k) => i.rank = (k + 1) +
          (this.data.pagination.per * this.data.pagination.current) - this.data.pagination.per)

      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: true
      })
    },

    updateModelValue (value, context) {
      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        changed: true
      })
    },

    save () {

    },

    cancel () {
      this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'tvs' } }, true)
    },
  }
}
</script>

<style scoped>

</style>
