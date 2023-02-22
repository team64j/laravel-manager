<template>
  <panel v-bind="$attrs" id="tvs" class="py-3" @action="action">
    <template #header v-if="msg">
      <div class="alert alert-blue mb-3 mx-4" v-html="$root.lang('tmplvars_management_msg')"/>
    </template>

    <template #top>
      <router-link :to="{ name: 'Tv', params: { id: null } }" class="btn-sm btn-green mr-1" role="button">
        <i class="fa fa-plus-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('new_tmplvars') }}</span>
      </router-link>

      <router-link :to="{ name: 'TvSort' }" class="btn-sm btn-gray mr-1" role="button">
        <i class="fa fa-sort fa-fw"></i>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('template_tv_edit') }}</span>
      </router-link>

      <button class="btn-sm btn-gray mr-1" @click="msg=!msg">
        <i class="far fa-question-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('help') }}</span>
      </button>
    </template>
  </panel>
</template>

<script>
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'ElementTvs',
  components: { Panel },

  data () {
    return {
      msg: false,
      actions: {
        copy: {
          icon: 'far fa-clone fa-fw hover:text-blue-500',
          help: this.$root.lang('duplicate'),
          helpFit: true,
          noOpacity: true
        },
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

      if (this.$attrs.columns) {
        const actions = {}

        if (this.$root.hasPermissions(['new_template', 'new_chunk', 'new_snippet'])) {
          actions.copy = this.actions.copy
        }

        if (this.$root.hasPermissions(['delete_template', 'delete_chunk', 'delete_snippet'])) {
          actions.delete = this.actions.delete
        }

        if (Object.values(actions).length) {
          if (!this.$attrs.columns.some(i => i.name === 'actions')) {
            this.$attrs.columns.push({
              label: this.$root.lang('onlineusers_action'),
              name: 'actions',
              actions: actions,
              width: '8rem',
              style: {
                textAlign: 'center'
              }
            })
          }
        } else {
          this.$attrs.columns = this.$attrs.columns.filter(i => i.name !== 'actions')
        }
      }
    },

    copy () {

    },

    delete () {
      if (confirm(this.$root.lang('confirm_delete_tmplvars'))) {
        //
      }
    }
  }
}
</script>
