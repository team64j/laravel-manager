<template>
  <div class="flex flex-col">

    <title-component/>

    <div v-if="data">

    </div>

    <div v-else class="flex items-center justify-center grow">
      <loader/>
    </div>

  </div>
</template>

<script>
import TitleComponent from '@/components/Layout/Title.vue'

export default {
  name: 'SchedulesPage',
  components: { TitleComponent },

  data () {
    return {
      data: null
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
    get () {
      this.data = []

      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        loading: false
      })
    }
  }
}
</script>

<style scoped>

</style>
