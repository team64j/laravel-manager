<template>
  <div class="header">
    <div class="menu">
      <a>
        <loader-icon v-if="loading"/>
        <i v-else class="fa fa-refresh" @click="reload"/>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TreeMenu',

  data () {
    return {
      loading: false
    }
  },

  methods: {
    reload () {

    }
  }
}
</script>

<style scoped>

</style>
