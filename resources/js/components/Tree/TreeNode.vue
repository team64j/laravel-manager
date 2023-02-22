<template>
  <li
      :class="this.class"
      :data-level="level">
    <span :style="{ paddingLeft: (level * 18) + 8 + 'px' }" class="node">
      <span v-if="node['loading']" class="toggle">
        <loader-icon/>
      </span>
      <span v-else-if="node['folder'] && !node['hideChildren']"
            class="toggle"
            @click.stop="$emit('action', 'toggle', node)">
        <i v-if="node?.['data']" class="fa fa-angle-down fa-fw"/>
        <i v-else class="fa fa-angle-right fa-fw"/>
      </span>

      <i class="icon-node" :class="icon">
        <i v-if="node['private']" class="fa fa-lock lock"/>
      </i>

      <span @click="$emit('action', 'click', $event, node, category)"
            @contextmenu.prevent="contextmenu"
            @mouseenter="mouseenter"
            @mousemove="mousemove"
            @mouseleave="mouseleave"
            class="title"
            :title="title">
        {{ name }}
      </span>

      <span v-if="node['size'] !== undefined" class="pl-2 text-sm size">{{ node['size'] }}</span>
      <span v-if="node['date'] !== undefined" class="pl-2 text-sm date">{{ node['date'] }}</span>
      <span v-if="node['id'] !== undefined" class="pl-2 text-sm id">({{ node['id'] }})</span>

      <span v-if="help" v-html="help" class="help"/>
    </span>

    <ul v-if="node?.['data']?.['data'] && node['data']?.['data'].length">
      <tree-node
          v-for="child in node['data']['data']"
          v-bind="Object.assign({}, $props, { node: child, level: level + 1 })"
          @action="action"/>

      <li v-if="node['data']?.['pagination']?.['next']" @click.stop="more(node['data']['pagination']['next'])">
        <a class="more">
          <loader-icon v-if="node['loading']"/>
          <span v-else>{{ $root.lang('paging_next') }}</span>
        </a>
      </li>
    </ul>
  </li>
</template>

<script>
export default {
  name: 'TreeNode',

  props: [
    'node',
    'id',
    'url',
    'route',
    'category',
    'keyTitle',
    'level',
    'aliases',
    'icons',
    'templates',
    'contextMenu',
    'currentRoute'
  ],

  computed: {
    class () {
      const classes = []

      if (this.node['class']) {
        this.node['class'].split(' ').forEach(i => {
          classes.push(i)
        })
      }

      if (this.node['folder'] !== undefined || !!this.node['data']) {
        classes.push('folder')
      }

      if (!!this.node['data']) {
        classes.push('opened')
      }

      if (this.node['hidden']) {
        classes.push('hide')
      }

      if (this.node['inhidden'] !== undefined && !this.node['inhidden']) {
        classes.push('inhidden')
      }

      if (this.node['unpublished'] || this.node['published'] !== undefined && !this.node['published']) {
        classes.push('unpublished')
      }

      if (this.node['deleted']) {
        classes.push('deleted')
      }

      if (this.currentRoute['name'] === this.route
          && (parseInt(this.currentRoute['params']?.id?.toString()) === this.node?.['id'] ||
              (this.currentRoute['params']?.['id'] && this.currentRoute['params']['id'] === this.node?.['key']))
          && (!this.node['folder'] && this.category || !this.category)
      ) {
        classes.push('active')
      }

      return classes
    },
    icon () {
      let icon = ''

      switch (true) {
        case !!this.icons[this.node['id']]:
          icon = this.icons[this.node['id']]
          break

        case !!this.icons[this.node['type']]:
          icon = this.icons[this.node['type']]
          break

        case !!this.node['folder']:
          if (this.node?.['data']?.['data']?.length) {
            icon = this.icons.folderOpen
          } else {
            icon = this.icons.folder
          }
          break

        default:
          icon = this.icons.default
      }

      return icon
    },
    title () {
      if (this.templates?.title) {
        let data = this.templates.title
        let cleanKeys = true

        return data.replace(/\{([\w.]*)}/g, (str, key) => {
          const value = typeof this.node[key] !== undefined ? this.node[key] : ''
          return (value === null || value === undefined) ? (cleanKeys ? '' : str) : value
        })
      }
    },
    help () {
      if (this.templates?.help) {
        let data = this.templates.help
        let cleanKeys = true

        return (data.replace(/\{([\w.]*)}/g, (str, key) => {
          const value = typeof this.node[key] !== undefined ? this.node[key] : ''
          return new Option((value === null || value === undefined) ? (cleanKeys ? '' : str) : value).innerHTML
        })).replace(/\r\n+/g, '<br>')
      }
    },
    name () {
      return this.node[this.keyTitle]
    }
  },

  data () {
    this.timer = 0

    return {}
  },

  created () {
    this.alias(this.node)
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
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

    more (url) {
      this.$emit('action', 'loader', 1)
      this.node.loading = true
      axios.get(url).then(r => {
        this.node['data']['pagination']['next'] = r.data.data?.pagination?.['next'] || null

        if (r.data.data.data) {
          this.node['data']['data'].push(...r.data.data.data)
        }

        this.$emit('action', 'loader', 0)
        this.node.loading = false
      })
    },

    contextmenu (event) {
      if (this.contextMenu) {
        event.preventDefault()
        this.mouseleave()

        console.log(event)
      }
    },

    mouseenter () {
      if (this.templates?.help) {
        this.timer = setTimeout(() => {
          clearTimeout(this.timer)
          this.$el.classList.add('hover')
        }, 1000)
      }
    },

    mousemove (event) {
      if (this.templates?.help) {
        const help = this.$el.querySelector('.help')
        help.style.left = event.clientX + 16 + 'px'
        help.style.top = event.clientY + 16 + 'px'

        if (event.clientY + 16 + help.offsetHeight > window.innerHeight) {
          help.style.top = event.clientY - help.offsetHeight + 'px'
        }
      }
    },

    mouseleave () {
      if (this.templates?.help) {
        clearTimeout(this.timer)
        this.$el.classList.remove('hover')
      }
    }
  }
}
</script>

<style scoped>
li .node {
  @apply text-gray-500 flex items-center whitespace-nowrap relative pl-8 pr-2 py-0.5 h-8
}
li > .node::after {
  @apply z-10 absolute left-0.5 top-0 right-0.5 bottom-0 rounded pointer-events-none transition;
  content: "";
}
li > .node:hover::after {
  @apply bg-white/5
}
li.active > .node::after {
  @apply bg-white/10
}
.toggle {
  @apply w-5 h-5 -ml-6 mr-1 inline-flex items-center text-gray-400 text-center rounded hover:text-gray-300 hover:bg-cms-700
}
.toggle i {
  @apply cursor-pointer
}
.icon {
  @apply mr-2 text-gray-300 hover:text-gray-100
}
.icon > i {
  @apply float-left text-xs text-rose-500 relative
}
.icon-node {
  @apply w-7 h-5 grow-0 shrink-0 text-center;
  font-size: 1.25rem;
}
.icon-node > .lock {
  @apply text-rose-600 text-sm -ml-3 w-3
}
.node .title {
  @apply pl-1 truncate grow cursor-pointer text-gray-400 hover:text-gray-300
}
.node .size {
  @apply text-gray-400/50
}
.node .date {
  @apply text-gray-400/80
}
.help {
  @apply fixed z-50 w-72 fixed p-4 text-sm text-left font-normal font-sans whitespace-normal rounded text-white bg-gray-800 dark:text-gray-800 dark:bg-white shadow-lg opacity-0 invisible transition
}
.hover > .node > .help {
  @apply opacity-90 visible
}
.hide > .node > .title {
  @apply text-gray-600
}
.hide > .node > .title:hover {
  @apply text-gray-500
}
.inhidden > .node > .title {
  @apply text-blue-300 hover:text-blue-200
}
.unpublished > .node > .title {
  @apply italic text-rose-500 hover:text-rose-400
}
.deleted > .node > .title {
  @apply not-italic line-through text-rose-800 hover:text-rose-700
}
li .more {
  @apply block relative cursor-pointer text-center text-amber-400 hover:text-amber-300 pr-8
}
</style>
