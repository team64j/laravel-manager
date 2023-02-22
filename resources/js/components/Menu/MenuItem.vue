<template>
  <li :id="[item['key'] ? `item-`+item['key'] : null]"
      :class="[item['class'], item['data'] ? 'parent' : null]"
      @mouseenter.stop.prevent="hover"
      @mouseout="unhover"
      @click="click">

    <menu-item-filter
        v-if="item['filter']"
        @input="action('filter', item, parent, $event)"/>

    <router-link
        v-else-if="item['to']"
        :to="item['to']"
        :class="[item['item.class'], item['disabled'] ? 'disabled' : '']"
        :title="item['title']">

      <img v-if="item['image']" :src="item['image']" alt="">

      <i v-else-if="item['icon']" class="grow-0 shrink-0" :class="item['icon']"/>

      <span v-if="item['name']" class="title truncate grow">{{ item['name'] }}</span>

      <i v-if="item['locked']" class="fa fa-lock lock"/>

      <span v-if="item['id']" class="text-xs ml-1">({{ item['id'] }})</span>

      <span v-if="item['html']" v-html="item['html']"/>

      <span class="toggle">
        <i v-if="item['data'] && !item['loading']" class="fa fa-angle-right"/>
        <loader-icon v-else-if="item['loading']"/>
      </span>
    </router-link>

    <a v-else
       :href="item['href']"
       :target="item['target']"
       :class="[item['item.class'], item['disabled'] ? 'text-rose-500' : '']"
       :title="item['title']"
       @click="$emit('action', item['click'], item, $event)">

      <img v-if="item['image']" :src="item['image']" alt="">

      <i v-else-if="item['icon']" :class="item['icon']">
        <i v-if="item['innerIcon']" :class="item['innerIcon']" :title="item['innerTitle']"/>
      </i>

      <span v-if="item['name']">{{ item['name'] }}</span>

      <span v-if="item['html']" v-html="item['html']"/>

      <i v-if="item['locked']" class="fa fa-lock lock"/>

      <template v-if="item['data']">
        <span class="toggle">
          <i v-if="!item['loading']" class="fa fa-angle-right"/>
          <loader-icon v-else/>
        </span>
      </template>
    </a>

    <ul v-if="item['data'] && item['data'].length">
      <menu-item
          v-for="node in item['data']"
          :key="node['key']"
          :item="node"
          :parent="item"
          @action="action"/>
    </ul>

    <ul v-else-if="item['data'] === null">
      <li>1</li>
    </ul>
  </li>
</template>

<script>
import MenuItemFilter from './MenuItemFilter.vue'

let counter

export default {
  name: 'MenuItem',
  components: { MenuItemFilter },
  props: {
    item: Object,
    parent: Object
  },
  methods: {
    action () {
      typeof this[arguments[0]] === 'function' && this[arguments[0]](...Array.from(arguments).splice(1)) ||
      this.$emit('action', ...arguments)
    },

    hover (event) {
      clearTimeout(counter)

      const target = event.currentTarget

      if (target.classList.contains('hover')) {
        return
      }

      this.$parent.$el.querySelectorAll('li.hover').forEach(i => i.classList.remove('hover'))
      target.classList.add('hover')

      if (this.$root.$refs.menu.$el.classList.contains('active')) {
        this.load()
      }
    },

    unhover () {
      if (!this.$parent.$el.querySelector(':scope > :hover')) {
        clearTimeout(counter)
      }
    },

    click () {
      if (!this.$root.$refs.menu.$el.classList.contains('active')) {
        this.load()
      }
    },

    load () {
      if (this.item['url'] && this.item['data']) {
        this.item['data'] = []
        this.item['loading'] = false

        counter = setTimeout(() => {
          this.item['loading'] = true

          axios.get(this.item['url']).then(r => {
            const data = r.data.data

            this.item['loading'] = false

            this.item['data'] = Array.from(this.item['data.items'] || [])

            if (data?.pagination?.next) {
              this.item['data'].push({
                filter: true,
                timer: 0,
                path: this.item['url']
              })
            }

            this.item['data'].push(...(data?.data || []).map(i => {
              i.to = { name: this.item['data.to'], params: { id: i.id } }

              return i
            }))
          })
        }, 160)
      }
    }
  },
  created () {
    for (let i in this.item['icons']) {
      if (this.$root[i] !== undefined) {
        for (let j in this.item['icons'][i]) {
          if (this.item['icons'][i][j].key === this.$root[i]) {
            this.item['icon'] = this.item['icons'][i][j].value
          }
        }
      }
    }
  }
}
</script>

<style scoped>
li a .lock, li a.disabled .title {
  @apply text-rose-500
}
li a .lock {
  @apply grow-0 text-rose-500 text-xs
}
</style>
