<template>
  <panel :data="data" id="usersCategories">
    <template #top>
      <router-link :to="{ name: 'UsersPermission', params: { id: null } }" class="btn-sm btn-green mr-1">
        <i class="fa fa-plus-circle fa-fw"/>
        <span class="hidden md:inline-block pl-1">{{ $root.lang('new_permission') }}</span>
      </router-link>
    </template>
  </panel>
</template>

<script>
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'UsersPermissions',
  components: { Panel },

  props: {
    data: [null, Object, Array]
  }
}
</script>

<style scoped>

</style>
