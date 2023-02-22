<template>
  <div class="flex flex-col">
    <layout v-if="type === 'json'" :data="data" :meta="meta" :layout="layout"/>
    <iframe v-if="type === 'html'" :src="'web/module/exec/' + this.id"/>
  </div>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'

export default {
  name: 'ModuleExecPage',
  components: { Layout },

  props: {
    currentRoute: Object
  },

  data () {
    this.id = this.currentRoute?.['params']?.['id'] || null

    return {
      type: null,
      data: null,
      meta: null,
      layout: null
    }
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
      loading: true,
      meta: { title: this.$root.lang('run_module') }
    })

    this.get()
  },

  watch: {
    '$root.dark' () {
      const styles = getComputedStyle(document.body)
      this.$el.firstElementChild.contentDocument.body.style.backgroundColor = styles.backgroundColor
      this.$el.firstElementChild.contentDocument.body.style.color = styles.color
    }
  },

  methods: {
    get () {
      axios.get('web/module/exec/' + this.id, {
        params: this.currentRoute['query'] || null
      }).then(r => {
        if (r.headers['content-type'] === 'application/json') {
          this.type = 'json'

          this.data = r.data?.data
          this.meta = r.data?.meta
          this.layout = r.data?.layout
        } else {
          this.type = 'html'

          setTimeout(() => {
            this.$el.firstElementChild.onload = function (event) {
              const styles = getComputedStyle(document.body)
              this.contentDocument.body.style.backgroundColor = styles.backgroundColor
              this.contentDocument.body.style.color = styles.color
            }
          }, 0)
        }

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          loading: false,
          meta: { title: this.$root.lang('run_module') }
        })
      })
    }
  }
}
</script>

<style scoped>
iframe {
  @apply absolute left-0 top-0 right-0 bottom-0 w-full min-h-full overflow-auto border-none bg-slate-100 dark:bg-cms-800 dark:text-white
}
</style>
