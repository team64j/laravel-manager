<template>
  <div class="tree" v-if="!needReload">
    <tree-menu v-if="menu"/>
    <div class="body">
      <div class="root">

        <div v-if="!data" class="text-center p-5">
          <loader-icon/>
        </div>

        <ul v-else-if="data?.['data']?.length">
          <tree-node
              v-for="node in data?.['data']"
              :node="node"
              :level="1"
              v-bind="$props"
              @action="action"
          />

          <li v-if="data?.['pagination']?.['next']" @click.stop="more(data['pagination']['next'])">
            <a class="more">
              <loader-icon v-if="data.loading"/>
              <span v-else>{{ $root.lang('paging_next') }}</span>
            </a>
          </li>
        </ul>

        <div v-else class="p-5 text-center">
          {{ $root.lang('not_set') }}
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import TreeMenu from './TreeMenu.vue'
import TreeNode from './TreeNode.vue'

export default {
  name: 'Tree',
  components: { TreeMenu, TreeNode },

  props: {
    id: {
      type: String,
      required: true
    },
    url: String,
    route: String,
    currentRoute: Object,
    category: Boolean,
    keyTitle: {
      type: String,
      default: 'title'
    },
    aliases: {
      type: Object,
      default: {
        id: 'id',
        data: 'data',
        date: 'date',
        size: 'size',
        type: 'type',
        title: 'title',
        folder: 'folder',
        parent: 'parent',
        hidden: 'hidden',
        inhidden: 'inhidden',
        private: 'private',
        deleted: 'deleted',
        disabled: 'disabled',
        published: 'published',
        visibility: 'visibility',
        unpublished: 'unpublished',
        hideChildren: 'hideChildren'
      }
    },
    icons: {
      type: Object,
      default: {}
    },
    templates: {
      type: Object,
      default: {}
    },
    menu: Object,
    contextMenu: Boolean
  },

  data () {
    this.keyStorage = 'tree.' + this.id.toLowerCase()

    this.iconsDefault = {
      default: 'far fa-file',
      folder: 'far fa-folder',
      folderOpen: 'far fa-folder-open'
    }

    return {
      loading: false,
      data: null,
      needReload: false,
      opened: this.$store.getters['Storage/get'](this.keyStorage)?.['opened'] ?? []
    }
  },

  created () {
    _.forEach(this.iconsDefault, (i, k) => {
      if (!this.icons[k]) {
        this.icons[k] = i
      }
    })

    this.$watch(
        () => this.$store.state.tree,
        data => {
          if (this.id === data?.id && typeof this[data?.action] === 'function') {
            this[data.action](data.params)
          }
        }
    )
  },

  mounted () {
    this.get()

  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    loader (a) {
      this.loading = !!a
    },

    get () {
      axios.get(this.url, {
        params: {
          opened: this.opened.length ? this.opened.join(',') : null
        }
      }).then(r => {
        this.data = r.data.data
      }).finally(() => {
        this.loader(0)
      })
    },

    toggle (node) {
      node['loading'] = true
      this.loader(1)
      if (node['data']) {
        this.removeOpened(node)
        node['data'] = null
        node['loading'] = false

        this.loader(0)
      } else {
        axios.get(this.url, {
          params: {
            parent: node['id'] ?? node['key']
          }
        }).then(r => {
          this.setOpened(node)
          node['data'] = r.data.data
        }).finally(() => {
          this.loader(0)
          node['loading'] = false
        })
      }
    },

    setOpened (node) {
      const id = node['id'] ?? node['key']
      const index = this.opened.indexOf(id)

      if (!~index) {
        this.opened.push(id)
        this.$store.dispatch('Storage/set', [this.keyStorage, { opened: this.opened }])
      }
    },

    removeOpened (node) {
      const id = node['id'] ?? node['key']
      const index = this.opened.indexOf(id)

      if (~index) {
        this.opened.splice(index, 1)
        this.$store.dispatch('Storage/set', [this.keyStorage, { opened: this.opened }])

        this.removeChildOpened(node)
      }
    },

    removeChildOpened (data) {
      for (const node of data.data.data) {
        const id = node['id'] ?? node['key']

        if (node['folder'] && node['data']) {
          const index = this.opened.indexOf(id)

          if (~index) {
            this.opened.splice(index, 1)
            this.$store.dispatch('Storage/set', [this.keyStorage, { opened: this.opened }])

            this.removeChildOpened(node)
          }
        }
      }
    },

    more (url) {
      this.loader(1)
      this.data['loading'] = true
      axios.get(url).then(r => {
        this.data['pagination']['next'] = r.data.data?.pagination?.['next'] || null

        if (r.data.data.data) {
          this.data['data'].push(...r.data.data.data)
        }
      }).finally(() => {
        this.loader(0)
        this.data['loading'] = false
      })
    },

    click (event, node, category) {
      if ((category && node['folder'] || node['category']) && !node['hideChildren']) {
        this.toggle(node)
      }

      if ((this.category && node['folder']) || node['disabled']) {
        return
      }

      if (event.ctrlKey) {
        this.$parent.$emit('action', 'pushRouter', {
          name: this.route + 's',
          params: {
            id: node.id
          }
        })
      } else {
        if (this.$store.getters['Config/get']('treeSelectNode')) {
          this.$store.dispatch('Config/set', { treeSelectNode: node })
        } else {
          if (node.key) {
            this.$parent.$emit('action', 'pushRouter', {
              name: this.route,
              params: {
                id: node.key
              }
            })
          } else {
            this.$parent.$emit('action', 'pushRouter', {
              name: this.route,
              params: {
                id: node.id
              }
            })
          }
        }
      }
    },

    updateNode (node, data) {
      if (!data) {
        this.loader(1)
      }

      data = data || this.data.data
      node = this.alias(node)

      if (node.id) {
        for (const i in data) {
          if (data[i].id === node.id) {
            if (this.category && data[i]['folder']) {
              continue
            }

            if (data[i]['parent'] !== node['parent']) {
              const el = Object.assign({}, data[i], node)
              data.splice(i, 1)
              this.transfer(el)
            } else {
              data[i] = Object.assign(data[i], node)
            }

            this.loader(0)
            break
          } else if (data[i]?.['data']?.['data']) {
            this.updateNode(node, data[i]['data']['data'])
          }
        }
      }
    },

    transfer (node, data) {
      data = data || this.data.data

      // for (const i in data) {
      //   if (data[i].id === node['parent'] && data[i].folder) {
      //     data[i].data.data.push(node)
      //     console.log(node)
      //   } else if (data[i].folder && data[i]['data']) {
      //     this.transfer(node, data[i]['data'])
      //     console.log(data[i])
      //   }
      // }

      // for (const i in data) {
      //   if (data[i].id === node['parent'] && data[i].folder) {
      //     data[i].data.data.push(node)
      //   } else if(data[i]?.['data']?.['data']) {
      //     if (!data[i]['data']['data'].length) {
      //       //data.splice(i, 1)
      //     } else {
      //       //this.transfer(node, data[i]['data'])
      //     }
      //   }
      // }
    },

    alias (data) {
      for (const i in data) {
        if (this.aliases[i] && this.aliases[i] !== i) {
          data[this.aliases[i]] = data[i]
          delete data[i]
        }
      }

      return data
    },

    reload () {
      this.needReload = true
      this.$nextTick(() => {
        this.needReload = false
      })
    }
  }
}
</script>

<style scoped>
.tree {
  @apply relative h-full w-full flex flex-col flex-wrap h-full cursor-default
}
.header {
  @apply flex justify-between items-center grow-0 flex-grow-0 h-9 w-full bg-cms-900
}
.body {
  @apply flex-grow h-0 grow w-full relative transition-all duration-200
}
.root {
  @apply h-full overflow-hidden overflow-y-auto py-2
}
.toggle {
  @apply flex pt-2 px-3 h-full cursor-pointer
}
li .more {
  @apply block relative cursor-pointer text-center text-amber-400 hover:text-amber-300 pr-8
}
.menu {
  @apply w-full px-1 flex
}
.menu a {
  @apply px-1.5 h-8 w-9 m-0.5 rounded inline-flex items-center justify-center shrink-0 rounded bg-cms-700 hover:bg-cms-600 cursor-pointer
}
</style>

<style>
.app-tree .tab-row-container {
  @apply h-9 bg-cms-900
}
.app-tree .tab-row-container .prev, .app-tree .tab-row-container .next {
  @apply top-0 h-9
}
.app-tree .tab-row {
  @apply pt-0.5
}
.app-tree .tab-row .tab {
  @apply flex items-center px-2.5 py-0 h-8 mx-0.5 rounded
}
.app-tree .tab-row .tab.active {
  @apply text-white bg-blue-600 dark:bg-blue-600
}
.app-tree .tab-row .tab i {
  @apply w-5 text-center
}
</style>
