<template>
  <div>

    <actions-buttons :data="['save', 'cancel']" @action="action"/>

    <title-component/>

    <panel :data="data" id="plugins" class="py-3" @action="action" :current-route="currentRoute">
      <template #header>
        <div class="alert alert-blue mb-3 mx-4">
          <p>{{ $root.lang('plugin_priority_instructions') }}</p>
        </div>
      </template>
    </panel>

  </div>
</template>

<script>
import ActionsButtons from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'PluginSortPage',
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
    this.list({})
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
      this.$emit('action', 'toTab', { name: 'Elements', params: { element: 'plugins' } }, true)
    },

    async list (params) {
      const r = await axios.get('api/plugin/sort', {
        params: params || this.currentRoute['query'] || {}
      })
      this.data = r.data.data
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
      category.data.map((i, k) => i.priority = k)

      this.$emit('action', 'setTab', {
        changed: true
      })
    }
  }
}
</script>

<style scoped>

</style>
