<template>
  <panel :data="data" id="usersRoles">
    <template #header v-if="msg">
      <div class="alert alert-blue mb-3 mx-4" v-html="$root.lang('role_management_msg')"/>
    </template>

    <template #top>
      <router-link :to="{ name: 'UsersRole', params: { id: null } }" class="btn-sm btn-green mr-1">
        <i class="fa fa-plus-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('new_role') }}</span>
      </router-link>

      <span class="btn-sm btn-gray mr-1" @click="msg=!msg">
        <i class="far fa-question-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('help') }}</span>
      </span>
    </template>
  </panel>
</template>

<script>
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'UsersRoles',
  components: { Panel },

  props: {
    data: [null, Object, Array]
  },

  data () {
    return {
      msg: false
    }
  }
}
</script>

<style scoped>

</style>
