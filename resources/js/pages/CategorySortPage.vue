<template>
  <div>

    <actions-buttons-component :data="['save', 'cancel']" @action="action"/>

    <title-component/>

    <panel :data="data" id="categories" class="py-3" @action="action" :current-route="currentRoute"/>

  </div>
</template>

<script>
import ActionsButtonsComponent from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'CategorySortPage',
  components: { ActionsButtonsComponent, TitleComponent, Panel },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: []
    }
  },

  created () {
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

    save () {

    },

    cancel () {
      this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'categories' } })
    },

    get (params) {
      axios.get('api/category/sort', {
        params: params || this.currentRoute['query'] || {}
      }).then(r => {
        this.data = r.data.data

        this.$emit('action', 'setTab', {
          changed: false
        })
      })
    },

    filter (str) {
      if (!str || str.length > 1) {
        this.$emit(
            'action',
            'pushRouter',
            str ? '?filter=' + str : ''
        )
      }
    },

    sortable (event, category) {
      category.data.map((i, k) => i.rank = k + 1)

      this.$emit('action', 'setTab', {
        changed: true
      })
    },
  }
}
</script>

<style scoped>

</style>
