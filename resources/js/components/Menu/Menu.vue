<template>
  <ul class="app-menu" @click.stop="click">
    <li v-for="group in data">
      <ul class="nav">
        <menu-item v-for="(item, index) in group['data']" :key="index" :item="item" @action="action"/>
      </ul>
    </li>
  </ul>
</template>

<script>
import MenuItem from './MenuItem.vue'

export default {
  name: 'Menu',
  components: { MenuItem },
  data () {
    return {
      data: this.$store.state['menu']
    }
  },

  mounted () {
    document.addEventListener('click', () => {
      if (this.$el.parentElement.classList.contains('active')) {
        this.$el.parentElement.classList.remove('active')
        this.$el.classList.remove('active')
      }
    })
  },

  methods: {
    action () {
      typeof this[arguments[0]] === 'function' && this[arguments[0]](...Array.from(arguments).splice(1)) ||
      this.$emit('action', ...arguments)
    },

    click (event) {
      const target = event.target
      const link = target.closest('a')

      if (target.tagName === 'INPUT' || link && link.classList.contains('events-none')) {
        return
      }

      if (target.closest('.app-menu ul.nav > li.parent > a')) {
        if (this.$el.classList.contains('active')) {
          this.$el.classList.remove('active')
          this.$el.parentElement.classList.remove('active')
        } else {
          this.$el.classList.add('active')
          this.$el.parentElement.classList.add('active')
        }
      } else {
        this.$el.classList.remove('active')
        this.$el.parentElement.classList.remove('active')
      }
    },

    filter (item, parent, event) {
      clearTimeout(item['timer'])

      if (event.target.value === '' || event.target.value.length > 2) {
        item['timer'] = setTimeout(() => {
          axios.get(item['path'] + (event.target.value ? '?filter=' + event.target.value : '')).then(r => {
            let index = parent.data.indexOf(item)
            parent.data.splice(index + 1)
            parent.data.push(...(r.data?.data?.data || []).map(i => {
              i.to = { name: parent['data.to'], params: { id: i.id } }

              return i
            }))
          })
        }, 200)
      }
    },

    toggleSearch (item, event) {
      const target = event.currentTarget
      let search = target.parentElement.querySelector('.search')
      let input = null

      if (!search) {
        search = document.createElement('div')
        search.className = 'search'
        search.innerHTML = '<input type="text" class="px-3 py-1"/>'
        target.parentElement.appendChild(search)
      }

      input = search.querySelector('input')
      input.focus()

      this.$el.classList.toggle('active')
      this.$el.parentElement.classList.toggle('active')
    }
  }
}
</script>

<style>
.app-menu {
  @apply flex h-full m-0 px-1
}
.app-menu > li:first-child {
  @apply grow
}
.app-menu .nav {
  @apply flex items-center h-full
}
.app-menu .nav > li {
  @apply h-full shrink-0 md:relative
}
.app-menu .nav > li > a {
  @apply flex h-10 items-center rounded my-1.5 mx-0.5 px-2 md:px-3 xl:px-4 relative select-none cursor-pointer transition-all duration-100
}
.app-menu .nav > li.hover > a {
  @apply bg-cms-600
}
.app-menu.active .nav > li.parent.hover > a {
  @apply bg-cms-600 text-gray-50 dark:bg-cms-600 dark:text-gray-200
}
.app-menu .nav > li:hover > a {
  @apply text-gray-100
}
.app-menu .nav li > a > i + span, .app-menu .nav li > a > img + span, .app-menu .home sub {
  @apply md:inline-block ml-2
}
.app-menu .nav > li > a > i.md\:hidden + span {
  @apply ml-0
}
.app-menu .nav > li > a > i + span, .app-menu .nav > li > a > img + span {
  @apply hidden md:inline-block
}
.app-menu .nav > li > a .toggle {
  @apply hidden md:inline-flex items-center justify-center m-0 -mr-2 ml-1 p-0 w-6 rotate-90
}
.app-menu .nav > li > a .toggle i {
  @apply text-xs rotate-0 transition
}
.app-menu.active .nav > li.hover > a .toggle i {
  @apply rotate-180
}
.app-menu .nav > li li {
  @apply h-10
}
.app-menu .nav > li li > a .toggle {
  @apply absolute top-0 right-0 h-full inline-flex items-center justify-center px-3 opacity-50
}
.app-menu .nav > li ul {
  @apply md:w-72 bg-white text-gray-900 opacity-0 invisible absolute left-1.5 md:left-0.5 md:top-full right-1.5 md:right-auto mt-1 z-10 py-1 shadow-xl shadow-black/30 rounded divide-y divide-gray-100 dark:bg-cms-600 dark:text-gray-300 dark:divide-zinc-700 transition-none
}
.app-menu > li:last-child > .nav > li ul {
  @apply md:left-auto md:right-0.5
}
.app-menu.active .nav li.hover > ul {
  @apply opacity-100 visible transition duration-100
}
.app-menu .nav > li > ul > li > ul {
  @apply top-0 mt-0 -mx-1 left-full overflow-y-auto;
  max-height: calc(100vh - 4rem);
}
.app-menu .nav > li > ul li > a {
  @apply flex items-center relative pl-6 pr-4 h-full
}
.app-menu .nav > li > ul li.hover > a[href], .app-menu .nav > li > ul li:hover > a[href] {
  @apply bg-blue-600 text-white
}
.app-menu .nav img {
  @apply inline-block h-8
}
.app-menu .site-status {
  @apply text-amber-400 absolute left-1/2 top-1
}
.app-menu .fa, .app-menu .far {
  @apply text-center w-6;
  font-size: 1.25rem;
}
.app-menu li .search {
  @apply flex items-center absolute top-1.5 right-full px-1.5 -mx-2 h-10 bg-cms-600 rounded opacity-0 invisible transition
}
.app-menu.active li.hover .search {
  @apply opacity-100 visible
}
.app-menu li .search input {
  @apply px-2 py-1 text-sm rounded
}
</style>
