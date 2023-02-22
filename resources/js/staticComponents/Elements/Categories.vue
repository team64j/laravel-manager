<template>
  <panel v-bind="$attrs" id="categories" class="py-3" @action="action">
    <template #top>
      <router-link :to="{ name: 'Category', params: { id: null } }" class="btn-sm btn-green mr-1">
        <i class="fa fa-plus-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('cm_add_new_category') }}</span>
      </router-link>

      <router-link :to="{ name: 'CategorySort' }" class="btn-sm btn-gray mr-1" role="button">
        <i class="fa fa-sort fa-fw"></i>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('cm_sort_categories') }}</span>
      </router-link>
    </template>
  </panel>
</template>

<script>
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'ElementCategories',
  components: { Panel },

  data () {
    return {
      actions: {
        delete: {
          icon: 'fa fa-trash fa-fw hover:text-rose-600',
          help: this.$root.lang('delete'),
          helpFit: true,
          noOpacity: true
        }
      }
    }
  },

  created () {
    this.additional()
  },

  updated () {
    this.additional()
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      }
    },

    additional () {
      if (!this.$attrs?.data) {
        return
      }

      if (this.$attrs.columns && !this.$attrs.columns.some(i => i.name === 'actions')) {
        this.$attrs.columns.push({
          label: this.$root.lang('onlineusers_action'),
          name: 'actions',
          actions: this.actions,
          width: '8rem',
          style: {
            textAlign: 'center'
          }
        })
      }
    },

    copy () {

    },

    delete (item, category) {
      if (confirm(this.$root.lang('confirm_delete_template'))) {
        //
      }
    }
  }
}
</script>

<style scoped>

</style>
