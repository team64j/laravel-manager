<template>
  <div v-if="data" class="flex flex-col">
    <actions-buttons :data="['save', 'delete', 'copy']" @action="action"/>

    <title-component :title="title" :id="null"/>

    <div class="px-6 py-4 bg-white dark:bg-cms-700">

      <TvInput label="Name" :value="data?.basename"/>

      <div v-if="/image\//.test(data.type)" class="image mb-4">
        <img :src="`../` + data.path" alt="">
      </div>

      <CodeEditor v-if="data?.content"
                  label="Editor"
                  :model-value="data.content"
                  :config="[{
                    component: 'Codemirror',
                    lang: lang
                  }]"/>
    </div>

  </div>

  <div v-else class="flex items-center justify-center">
    <loader/>
  </div>
</template>

<script>
import ActionsButtons from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'
import TvInput from '@/components/Fields/Input.vue'
import CodeEditor from '@/components/CodeEditor.vue'

export default {
  name: 'FilePage',
  components: { CodeEditor, TvInput, ActionsButtons, TitleComponent },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null
    }
  },

  computed: {
    title () {
      return this.data?.['basename'] || '...'
    },
    lang () {
      let lang = ''

      switch (this.data?.ext) {
        case 'css':
        case 'less':
        case 'cass':
          lang = 'css'
          break

        case 'vue':
          lang = 'vue'
          break

        case 'js':
        case 'json':
          lang = 'javascript'
          break

        case 'xml':
        case 'svg':
          lang = 'xml'
          break

        case 'php':
          lang = 'php'
          break

        case 'htm':
        case 'html':
          lang = 'html'
          break

        case 'md':
            lang = 'markdown'
          break

        case 'sql':
            lang = 'sql'
          break
      }

      if (!lang) {
        switch (this.data?.type) {
          case 'application/json':
            lang = 'javascript'
            break
        }
      }

      if (this.data?.lang) {
        lang = this.data.lang
      }

      return lang
    }
  },

  watch: {
    'currentRoute.params.id' (key, newKey) {
      if (this.currentRoute['name'] !== 'File' ||
          (this.currentRoute['name'] === 'File' && (key === newKey || key === this.data?.path))) {
        return
      }

      this.get()
    }
  },

  created () {
    this.get()
  },

  methods: {
    action () {

    },

    get () {
      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        meta: { title: '...' },
        changed: false,
        loading: true
      })

      this.data = null

      axios.get('api/file/' + this.currentRoute['params']['id'], {
        params: this.currentRoute['query'] || {}
      }).then(r => {
        this.data = r.data.data

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          meta: { title: this.title },
          changed: false,
          loading: false
        })
      })
    }
  }
}
</script>

<style scoped>
.image {
  @apply relative
}
.image img {
  @apply relative
}
</style>
