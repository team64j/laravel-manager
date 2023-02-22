<template>
  <div class="flex flex-nowrap overflow-hidden h-full">
    <div v-show="panelSidebar" class="files-tree grow-0 shrink-0 border-r">
      <tree url="api/files/tree" id="Files" route="Files" @action="action" :current-route="currentRoute"/>
    </div>
    <div class="files-main">
      <div class="files-data">
        <panel ref="panel" :data="{ ...data }" :current-route="currentRoute" class="pb-4" :class="panelView" @action="action">
          <template #header>
            <div class="files-header">
              <div>
                <button type="button" class="btn-sm btn-gray mx-0.5" @click="toggleSidebar">
                  <i class="fa fa-bars w-5"/>
                </button>
              </div>
              <div>
                <button type="button" class="btn-view btn-view-icons btn-sm btn-gray mx-0.5" @click="toggleViewIcons">
                  <i class="fa fa-th w-5"/>
                </button>
                <button type="button" class="btn-view btn-view-list btn-sm btn-gray mx-0.5" @click="toggleViewList">
                  <i class="fa fa-list w-5"/>
                </button>
                <button type="button" class="btn-sm btn-green mx-0.5">{{ $root.lang('files_uploadfile') }}</button>
              </div>
            </div>
          </template>
        </panel>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@/components/Layout/Layout.vue'
import Tree from '@/components/Tree/Tree.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'FilesPage',
  components: { Panel, Tree, Layout },

  props: {
    currentRoute: {
      type: Object
    },
    modelValue: {
      type: [null, Object, String, Number, Boolean],
      default: null
    }
  },

  data () {
    this.keyView = 'CMS.PANEL.FILES.VIEW'
    this.keySidebar = 'CMS.PANEL.FILES.SIDEBAR'

    return {
      data: null,
      meta: null,
      layout: null,
      panelView: this.$store.getters['Storage/get']('panelFilesView', 'panel-list'),
      panelSidebar: this.$store.getters['Storage/get']('panelFilesSidebar', true)
    }
  },

  created () {
    this.get()
  },

  watch: {
    'currentRoute.params.id' (key, newKey) {
      if (this.currentRoute['name'] !== 'Files' ||
          (this.currentRoute['name'] === 'Files' && (key === newKey || key === this.data?.path))) {
        return
      }

      this.get()
    }
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    get () {
      this.data = null

      axios.get('api/files' + (this.currentRoute?.['params']?.['id'] ? '/' + this.currentRoute['params']['id'] : ''), {
        params: this.currentRoute['query'] ?? {}
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta
        this.layout = r.data.layout
      }).finally(() => {
        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          changed: false,
          loading: false
        })
      })
    },

    toggleSidebar () {
      this.panelSidebar = !this.panelSidebar
      this.$store.dispatch('Storage/set', { panelFilesSidebar: this.panelSidebar })
    },

    toggleViewIcons () {
      this.$refs.panel.$el.classList.remove('panel-list')
      this.$refs.panel.$el.classList.add('panel-icons')
      this.$store.dispatch('Storage/set', { panelFilesView: 'panel-icons' })
    },

    toggleViewList () {
      this.$refs.panel.$el.classList.remove('panel-icons')
      this.$refs.panel.$el.classList.add('panel-list')
      this.$store.dispatch('Storage/set', { panelFilesView: 'panel-list' })
    }
  }
}
</script>

<style scoped>
.files-tree {
  @apply w-96 bg-slate-100 dark:bg-cms-800 text-gray-800
}
.files-main {
  @apply flex grow flex-col
}
.files-header {
  @apply sticky top-0 z-10 flex justify-between items-center px-2 py-2 border-b
}
.files-data {
  @apply grow overflow-auto
}
.btn-view {
  @apply border-transparent
}
</style>

<style>
.files-tree li .node .title {
  @apply text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300
}
.files-tree li > .node > * {
  @apply z-10
}
.files-tree li > .node::after {
  @apply z-0
}
.files-tree li:hover > .node:hover::after {
  @apply bg-slate-200/40 dark:bg-cms-600/30
}
.files-tree li.active > .node::after {
  @apply bg-slate-200/70 dark:bg-cms-600/60
}
.panel .data thead tr.columns th {
  @apply first:pl-0
}
.panel .data thead tr.columns th > div:first-child, .panel .data thead tr.columns th > div:last-child {
  @apply px-3
}
.panel .data thead tr.columns th > div {
  @apply py-1
}
.panel-icons .btn-view-icons, .panel-list .btn-view-list {
  @apply bg-blue-600 text-white hover:bg-blue-600
}
.panel-list .data tbody tr td {
  @apply first:p-0
}
.panel-list .data tbody tr td img {
  @apply w-auto h-7 m-0.5 inline-block
}
.panel-icons .data thead {
  @apply hidden
}
.panel-icons .data tbody {
  @apply flex flex-wrap
}
.panel-icons .data tbody tr {
  @apply grow flex flex-col justify-between rounded-md border-2 border-white dark:border-cms-700 items-center text-center w-1/3 md:w-3/12 lg:w-3/12 xl:w-1/12;
  min-width: 12rem;
  max-width: 20rem;
}
.panel-icons .data tbody tr td {
  @apply px-2 py-0 first:pt-4 first:pb-4 last:pb-2 text-sm
}
.panel-icons .data tbody tr td i {
  @apply text-7xl
}
.panel-icons .data tbody tr td img {
  @apply w-auto h-20
}
.panel-icons .data tbody tr td div {
  @apply text-center
}
</style>
