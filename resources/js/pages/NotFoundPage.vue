<template>
  <div class="p-8">
    <h1 class="text-2xl text-center">Page not found</h1>
  </div>
</template>

<script>
export default {
  name: 'NotFoundPage',

  created () {
    this.$emit('action', 'setTab', {
      meta: { title: 'Page not found' },
      changed: false
    })
  }
}
</script>

<style scoped>

</style>
