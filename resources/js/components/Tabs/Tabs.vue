<template>
  <div :id="id+`Pane`" class="tab-pane" :class="{ 'tab-pane-vertical': vertical }">

    <div class="tab-row-container">

      <div class="tab-row" ref="row">
        <div v-for="(tab, index) in data" :key="index" :title="tab.title" class="tab" :class="{ 'active' : tab.id === active }"
             @mousedown="select(tab, index)">
          <i v-if="tab.icon" class="icon" :class="tab.icon"/>
          <span v-if="tab.name">{{ tab.name }}</span>
        </div>
      </div>

      <i class="fa fa-angle-left prev disabled" @mousedown="prev" ref="prev"></i>
      <i class="fa fa-angle-right next disabled" @mousedown="next" ref="next"></i>
    </div>

    <template v-if="history">
      <template v-for="tab in data" :key="tab.id">
        <div v-if="tab.id === active"
             :id="`tab-`+tab.id"
             :class="tab.class"
             class="tab-page">
          <slot :name="tab.id"/>
        </div>
      </template>
    </template>

    <template v-else-if="loadOnce">
      <template v-for="tab in data" :key="tab.id">
        <div v-if="tab.loaded || tab.id === active" v-show="tab.id === active"
             :id="`tab-`+tab.id"
             :class="tab.class"
             class="tab-page">
          <slot :name="tab.id"/>
        </div>
      </template>
    </template>

    <template v-else>
      <template v-for="tab in data" :key="tab.id">
        <div v-show="tab.id === active"
             :id="`tab-`+tab.id"
             :class="tab.class"
             class="tab-page">
          <slot :name="tab.id"/>
        </div>
      </template>
    </template>

  </div>
</template>

<script>

export default {
  name: 'Tabs',
  props: {
    id: {
      type: String,
      required: true
    },
    data: {
      type: [null, Array, Object],
      required: true
    },
    uid: String,
    history: [Boolean, String],
    loadOnce: Boolean,
    watch: Boolean,
    vertical: {
      type: Boolean,
      default: false
    },
    currentRoute: Object
  },

  data () {
    this.keyStorage = this.$store.getters['Config/get']('remember_last_tab') ?
        'tabs.' + this.id.toLowerCase() : null

    return {
      active: null
    }
  },

  created () {
    if (this.history) {
      this.active = this.currentRoute['params'][this.history]
      this.$watch(
          () => this.currentRoute['params'][this.history],
          active => {
            if (!active) {
              return
            }

            if (this.active !== active) {
              this.active = active
            }
          }
      )
    } else if (this.keyStorage) {
      const active = this.$store.getters['Storage/get'](this.keyStorage)?.['active']

      if (this.data.some(i => i.id === active)) {
        this.active = active
      } else {
        this.active = this.data[0].id
      }

      this.data.forEach(tab => {
        if (this.loadOnce) {
          tab.loaded = tab.id === this.active
        }
      })
    } else {
      this.data.forEach(tab => {
        if (tab.active) {
          this.active = tab.id
        }

        tab.loaded = tab.active
      })
    }

    if (this.watch) {
      this.$watch(
          () => this.currentRoute['name'],
          active => {
            if (!active) {
              return
            }

            this.data.forEach((tab, index) => {
              if (typeof tab.route === 'object') {
                tab.active = tab.route.some(i => i === active)
              } else {
                tab.active = tab.route === active
              }

              if (tab.active) {
                this.select(tab, index)
              }
            })
          }
      )
    }
  },

  mounted () {
    this.init(this.data.findIndex(tab => tab.id === this.active))
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    select (tab, index) {
      this.active = tab.id

      this.init(index)

      if (this.history) {
        const route = {
          params: {}
        }

        route.params[this.history] = tab.id
        this.$emit('action', 'pushRouter', route)

      } else if (this.keyStorage) {
        this.$store.dispatch('Storage/set', [this.keyStorage, { active: this.active }])
      }

      if (this.loadOnce) {
        tab.loaded = true
      }
    },

    init (index) {
      let right = 0,
          width = 0

      this.$refs.row.styles = getComputedStyle(this.$refs.row)

      this.$refs.row.querySelectorAll('.tab').forEach((t, i) => {
        t.styles = getComputedStyle(t)

        if (i <= index) {
          width += t.offsetWidth + parseFloat(t.styles.marginLeft) + parseFloat(t.styles.marginRight)

          if (i < index) {
            right += t.offsetWidth + parseFloat(t.styles.marginLeft)
          }
        }
      })

      if (this.$refs.row.scrollLeft > right) {
        this.$refs.row.scrollLeft = right
      }

      if (this.$refs.row.offsetWidth < width) {
        this.$refs.row.scrollLeft = width - this.$refs.row.offsetWidth
      }

      if (index) {
        this.$refs.prev.classList.remove('disabled')
      } else {
        this.$refs.prev.classList.add('disabled')
      }

      if (this.data[index + 1]) {
        this.$refs.next.classList.remove('disabled')
      } else {
        this.$refs.next.classList.add('disabled')
      }
    },

    prev () {
      const index = this.data.findIndex(tab => tab.id === this.active)

      if (this.data[index - 1]) {
        this.active = this.data[index - 1].id
        this.init(index - 1)
      }
    },

    next () {
      const index = this.data.findIndex(tab => tab.id === this.active)

      if (this.data[index + 1]) {
        this.active = this.data[index + 1].id
        this.init(index + 1)
      }
    }
  }
}
</script>

<style scoped>
.tab-row-container {
  @apply overflow-hidden relative h-12 w-full
}
.tab-row {
  @apply relative z-20 overflow-hidden overflow-x-auto h-20 px-0 mx-5 pt-1 flex flex-nowrap
}
.tab-pane {
  @apply flex flex-wrap flex-col grow content-start
}
.tab-pane .tab {
  @apply py-3 px-4 h-12 relative cursor-pointer whitespace-nowrap rounded-t select-none text-center text-gray-600 hover:bg-gray-400/10 dark:text-gray-400 transition
}
.tab-pane .tab.active {
  @apply bg-white border-inherit text-gray-800 dark:bg-cms-700 dark:text-gray-100 shadow-md dark:shadow-black/20
}
.tab-pane .tab .icon + span {
  @apply ml-2
}
.tab-pane .prev, .tab-pane .next {
  @apply absolute h-11 w-5 top-1 pt-1 flex items-center justify-center cursor-pointer select-none hover:text-blue-500 transition
}
.tab-pane .prev {
  @apply left-0
}
.tab-pane .next {
  @apply right-0
}
.tab-pane .prev.disabled, .tab-pane .next.disabled {
  @apply opacity-0 scale-0
}
.tab-page {
  @apply w-full basis-0 content-start items-start grow bg-white dark:bg-cms-700 shadow
}
.tab-pane.tab-pane-vertical {
  @apply flex-row
}
.tab-pane-vertical > .tab-row-container {
  @apply grow-0 h-auto w-auto pl-4 pr-0 mb-0 -mr-[1px]
}
.tab-pane-vertical > .tab-row-container::after {
  @apply left-auto top-0
}
.tab-pane-vertical > .tab-row-container .tab-row {
  @apply flex-col h-auto pl-1 pr-0 py-4 m-0
}
.tab-pane-vertical > .tab-row-container .tab {
  @apply rounded-l rounded-r-none truncate max-w-[15rem]
}
.tab-pane-vertical > .tab-row-container .prev::before, .tab-pane-vertical > .tab-row-container .next::before {
  @apply rotate-90
}
.tab-pane-vertical > .tab-row-container .prev {
  @apply h-auto w-full py-1
}
.tab-pane-vertical > .tab-row-container .next {
  @apply h-auto w-full py-1 top-auto bottom-0
}
.tab-pane-vertical > .tab-page {
  @apply h-full grow basis-0 rounded-l
}
.tab-pane-vertical.tab-pane .prev, .tab-pane-vertical.tab-pane .next {
  @apply hidden
}
</style>

<style>
.panel > .tab-pane > .tab-row-container::before, .panel > .layout > .tab-pane > .tab-row-container::before {
  @apply absolute left-0 top-0 right-0 bottom-[1px] bg-slate-100 dark:bg-cms-800;
  content: "";
}
.panel > .tab-pane.tab-pane-vertical > .tab-row-container::before, .panel > .layout > .tab-pane.tab-pane-vertical > .tab-row-container::before {
  @apply bottom-0 right-[1px]
}
.tab-page .tab-pane-vertical {
  @apply overflow-hidden
}
.tab-page .tab-pane-vertical > .tab-page {
  @apply border-t-0 rounded-none rounded-l dark:shadow-none dark:bg-cms-600/40
}
.tab-page .tab-pane-vertical > .tab-row-container {
  @apply mr-0
}
.tab-page .tab-pane-vertical > .tab-row-container .tab {
  @apply dark:shadow-none
}
.tab-page .tab-pane-vertical > .tab-row-container .tab.active {
  @apply border-gray-200 dark:border-cms-600 dark:bg-cms-600/40
}
.tab-pane-vertical > .tab-page .tab-page {
  @apply px-8
}
</style>
