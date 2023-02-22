<template>
  <div class="flex flex-col">

    <title-component/>

    <panel v-if="data" :data="data" :current-route="currentRoute" class="py-6"/>

    <div v-else class="flex items-center justify-center grow">
      <loader/>
    </div>

  </div>
</template>

<script>
import TitleComponent from '@/components/Layout/Title.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'SystemInfoPage',
  components: { TitleComponent, Panel },

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
      loading: true
    })

    this.get()
  },

  methods: {
    get () {
      axios.get('api/systeminfo', {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          loading: false
        })
      })
    },
  }
}
</script>

<style scoped>

</style>
