<template>
  <div v-if="propData && renderComponent" class="panel" :class="$props.class" :id="idPanel">
    <slot name="header"/>

    <div class="flex justify-between px-2">
      <div class="grow-0 px-2" :class="{ 'pb-3': $slots.top }">
        <slot name="top"/>
      </div>
      <div v-if="propFilter === true" class="filter px-2 pb-3">
        <input type="text"
               class="rounded"
               :placeholder="$root.lang('element_filter_msg')"
               :class="{ 'active': currentRoute?.query?.filter }"
               :value="filterValue"
               @keyup="setFilter">
        <i class="fa fa-circle-xmark mr-2" @click="clearFilter"/>
      </div>
    </div>

    <div v-if="!propData.data" class="text-center p-5">
      <loader-icon/>
    </div>

    <div v-else-if="propData.data?.length" class="data" :class="dataClass">
      <table ref="data">

        <thead v-if="propColumns && propColumns.length">
        <tr class="columns">
          <th v-for="(column, index) in propColumns" :style="{ width: column['width'] }"
              :class="{ 'sortable': column['sort'] }">
            <div @click="columnSort(column)" :class="{ 'active': propData?.['sorting']?.['order'] === column.name }">
              <span>{{ column.label ?? column.name ?? '' }}</span>
              <i v-if="column.sort && propData['sorting']"
                 :class="[
                     propData['sorting']['order'] === column.name ?
                     (propData['sorting']['dir'] === 'desc' ?
                     'fa-sort-down' : 'fa-sort-up') : 'fa-sort'
                     ]"
                 class="fa fa-fw sort"/>
              <div class="splitter" :data-index="index"
                   @mouseenter="columnEnter"
                   @mousedown="columnDown"
                   @mouseout="columnOut"/>
            </div>
          </th>
        </tr>
        <tr v-if="propColumns.some(i => i.filter)" class="filters">
          <th v-for="column in propColumns" :style="{ width: column['width'] }">
            <div v-if="column.filter">
              <template v-if="column.filter?.type === 'date'">
                <input type="date" class="w-1/2 px-2 py-0"
                       :value="column.filter.data.from"
                       :min="column.filter.data.min"
                       :max="column.filter.data.to"
                       @input="filters($event, column)">
                <input type="date" class="w-1/2 px-2 py-0"
                       :value="column.filter.data.to"
                       :min="column.filter.data.from"
                       :max="column.filter.data.max"
                       @input="filters($event, column)">
              </template>
              <select v-else-if="column.filter?.data"
                      class="w-full px-2 pr-8 py-0"
                      @input="filters($event, column)">
                <option v-for="o in column.filter.data" :value="o.key" :selected="o.selected">{{ o.value }}</option>
              </select>
              <input v-else type="text" class="w-full px-2 py-0">
            </div>
          </th>
        </tr>
        </thead>

        <template v-if="propData.data && !propData.data[0]?.data">
          <tbody v-if="propRoute">
          <router-link v-for="item in propData.data" :to="{ name: propRoute, params: { id: item.id } }"
                       custom v-slot="{ navigate }">
            <tr @click="navigate" class="cursor-pointer" :class="{ 'disabled' : item.disabled }">
              <panel-cell
                  :columns="propColumns"
                  :category="propData.data"
                  :data="$parent.$parent.$parent['data']"
                  :item="item"
                  @action="action"
                  @update:modelValue="updateModelValue"/>
            </tr>
          </router-link>
          </tbody>

          <draggable v-else-if="propData?.draggable"
                     :list="propData.data"
                     item-key="id"
                     tag="tbody"
                     handle=".draggable-handle"
                     @end="sortable($event, propData)">
            <template #item="{ element: item }">
              <tr :class="{ 'disabled' : item.disabled }">
                <panel-cell :columns="propColumns"
                            :category="propData.data"
                            :data="$parent.$parent.$parent['data']"
                            :item="item"
                            @action="action"
                            @update:modelValue="updateModelValue"/>
              </tr>
            </template>
          </draggable>

          <tbody v-else>
          <tr v-for="item in propData.data"
              :class="{ 'disabled' : item.disabled, 'active': item.active }"
              @click="selectRow(item, propData.data, $event)">
            <panel-cell
                :columns="propColumns"
                :category="propData.data"
                :data="$parent.$parent.$parent['data']"
                :item="item"
                @action="action"
                @update:modelValue="updateModelValue"/>
          </tr>
          </tbody>
        </template>

        <template v-else>
          <template v-for="category in propData.data">
            <tbody v-if="category.name && category.data.length">
            <tr class="category"
                :class="[ category.closed ? 'closed' : '' ]"
                @mousedown="toggleCategory(category)">
              <td :colspan="propColumns && propColumns.length || Object.values(category.data[0]).length">
              <span class="toggle">
                <i :class="[!category.closed ? 'fa-square-minus' : 'fa-square-plus']"
                   class="far fa-fw mr-1"/>{{ category.name }} ({{ category.id }})</span>
              </td>
            </tr>
            </tbody>

            <template v-if="category.data && !category.closed">

              <tbody v-if="propRoute">
              <router-link v-for="item in category.data" :to="{ name: propRoute, params: { id: item.id } }"
                           custom v-slot="{ navigate }">
                <tr @click="navigate" class="cursor-pointer" :class="{ 'disabled' : item.disabled }">
                  <panel-cell
                      :columns="propColumns"
                      :category="category"
                      :data="$parent.$parent.$parent['data']"
                      :item="item"
                      @action="action"
                      @update:modelValue="updateModelValue"/>
                </tr>
              </router-link>
              </tbody>

              <draggable v-else-if="category.draggable"
                         :list="category.data"
                         item-key="id"
                         tag="tbody"
                         handle=".draggable-handle"
                         @end="sortable($event, category)">
                <template #item="{ element: item }">
                  <tr :class="{ 'disabled' : item.disabled, 'active': item.active }"
                      @click="selectRow(item, category.data, $event)">
                    <panel-cell
                        :columns="propColumns"
                        :category="category"
                        :data="$parent.$parent.$parent['data']"
                        :item="item"
                        @action="action"
                        @update:modelValue="updateModelValue"/>
                  </tr>
                </template>
              </draggable>

              <tbody v-else>
              <tr v-for="item in category.data"
                  :class="{ 'disabled' : item.disabled, 'active': item.active }"
                  @click="selectRow(item, category.data, $event)">
                <panel-cell
                    :columns="propColumns"
                    :category="category"
                    :data="$parent.$parent.$parent['data']"
                    :item="item"
                    @action="action"
                    @update:modelValue="updateModelValue"/>
              </tr>
              </tbody>

            </template>
          </template>
        </template>

      </table>
      <div class="data-mask" @mouseup="columnUp" @touchend="columnUp" @mousemove="columnMove" @touchmove="columnMove"/>
    </div>

    <div v-else class="p-5 text-center">
      {{ $root.lang('not_set') }}
    </div>

    <panel-pagination :data="propData['pagination']" @action="action"/>

  </div>
</template>

<script>
import draggable from 'vuedraggable'
import PanelCell from './PanelCell'
import PanelPagination from './PanelPagination'
import LoaderIcon from '@/components/Layout/LoaderIcon.vue'

let filterTimer = 0

export default {
  name: 'Panel',
  components: { LoaderIcon, draggable, PanelCell, PanelPagination },

  props: {
    currentRoute: Object,
    data: {
      type: [null, Object],
      default: null
    },
    route: {
      type: String
    },
    id: {
      type: String
    },
    class: {
      type: [String, Array, Object]
    },
    dataClass: {
      type: [String, Array, Object]
    },
    rerender: {
      default: false
    },
    history: [Boolean, String],
    url: String,
    columns: Array,
    filter: Boolean
  },

  data () {
    const id = this.id ?? `panel`

    this.keyStorage = 'panel.' + id.toLowerCase()

    return {
      propData: this.data || null,
      propColumns: this.columns || this.data?.columns || null,
      propFilter: this.filter || this.data?.filter || null,
      propRoute: this.route || this?.data?.route || null,
      propUrl: this.url ?? this.$route?.['meta']?.url ?? null,
      x: 0,
      idPanel: id,
      filterValue: null,
      columnSplitter: null,
      renderComponent: true
    }
  },

  watch: {
    data (data) {
      this.propData = data

      if (data?.columns && !this.propColumns) {
        this.propColumns = data.columns
      }

      this.setColumnsSettings()
    }
  },

  created () {
    this.filterValue = this.currentRoute?.['query']?.['filter']

    if (this.url) {
      this.get()
    }

    this.setColumnsSettings()
  },

  updated () {
    this.filterValue = this.currentRoute?.['query']?.['filter']
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    get (query) {
      const url = this.$router.resolve(this.propUrl)
      const path = this.propUrl.split('?')[0]

      query = Object.assign(url.query, query || this.currentRoute?.['query'] || {})

      if (this.propData?.data) {
        this.propData.data = null
      }

      if (this.propData?.pagination) {
        this.propData.pagination = null
      }

      axios.get(path, {
        params: query
      }).then(r => {
        this.propData = r.data.data || r.data.data?.data
        this.propColumns = r.data.data?.columns || r.data.data?.data?.columns || this.propColumns
        this.propFilter = r.data.data?.filter || r.data.data?.data?.filter || this.propFilter
        this.propRoute = r.data.data?.route || r.data.data?.data?.route || this.propRoute
      })
    },

    setColumnsSettings () {
      const settings = this.$store.getters['Storage/get'](this.keyStorage, {})
      const closed = settings.closed ?? []
      const width = settings.width ?? {}

      if (this.propData?.data) {
        this.propData.data.map(category => {
          if (category.data) {
            const index = closed.indexOf(category.id)
            category.closed = index > -1
          }
        })
      }

      if (this.propColumns) {
        this.propColumns.map((column, index) => {
          if (width[index]) {
            column.width = width[index]
          }
        })
      }
    },

    pagination (url) {
      if (this.propUrl) {
        url = this.$router.resolve('?' + url)
        if (this.history) {
          this.$emit(
              'action',
              'pushRouter',
              url
          )
        }

        this.get(url.query)
      } else {
        this.$emit('action', 'pagination', ...arguments)
      }
    },

    toggleCategory (category) {
      category.closed = !category.closed

      let closed = this.$store.getters['Storage/get'](this.keyStorage, {})?.['closed'] ?? []
      let index = closed.indexOf(category.id)

      if (category.closed) {
        !(index > -1) && closed.push(category.id)
      } else {
        index > -1 && closed.splice(index, 1)
      }

      this.$store.dispatch('Storage/set', [this.keyStorage, { closed }])
    },

    columnEnter (event) {
      this.columnSplitter = event.target
      this.columnSplitter.style.height = this.$refs.data.offsetHeight - 1 + 'px'
    },

    columnOut (event) {
      if (!this.$el.classList.contains('data-resize')) {
        this.columnSplitter = event.target
        this.columnSplitter.style.height = null
      }
    },

    columnUp (event, reset) {
      this.$el.classList.remove('data-resize')
      this.$el.querySelectorAll('.column-resize').forEach(el => el.classList.remove('column-resize'))
      const th = this.columnSplitter.closest('th'),
          w = th.offsetWidth - parseInt(this.columnSplitter.style.marginRight),
          width = this.$store.getters['Storage/get'](this.keyStorage, {})?.['width'] ?? {},
          index = this.columnSplitter.dataset.index

      this.columnSplitter.style.marginRight = null
      this.columnSplitter.style.height = null

      if (reset) {
        if (th.dataset.width) {
          th.style.width = th.dataset.width
          delete th.dataset.width
        } else {
          th.style.width = null
        }
        width[index] && delete width[index]
      } else {
        if (!th.dataset.width) {
          th.dataset.width = th.style.width
        }
        th.style.width = w + 'px'
        width[index] = w + 'px'
      }

      this.$store.dispatch('Storage/set', [this.keyStorage, { width }])
      document.onselectstart = () => null
    },

    columnDown (event) {
      this.columnSplitter = event.target
      this.x = event.clientX
      this.$el.classList.add('data-resize')
      this.columnSplitter.closest('th').classList.add('column-resize')
      this.columnSplitter.style.height = this.$refs.data.offsetHeight + 'px'
      document.onselectstart = () => false
    },

    columnMove (event) {
      const x = this.x - event.clientX

      if (x > this.columnSplitter.parentElement.offsetWidth) {
        this.columnUp(event, true)
        document.onselectstart = () => false
      } else {
        this.columnSplitter.style.marginRight = x + 'px'
      }
    },

    columnSort (column) {
      if (!column.sort) {
        return
      }

      const dir = this.propData?.['sorting']?.['dir'] === 'asc' ? 'desc' : 'asc'

      if (this.history) {
        const query = Object.assign({}, this.currentRoute['query'] || {})
        query['order'] = column['name']
        query['dir'] = dir

        this.get(query)

        this.$emit(
            'action',
            'pushRouter',
            { query }
        )
      } else {
        this.$emit('action', 'sort', column['name'], dir)
      }
    },

    setFilter (event) {
      clearTimeout(filterTimer)
      const filter = event.target.value.toLowerCase()

      if (filter) {
        event.target.classList.add('active')
      } else {
        event.target.classList.remove('active')
      }

      filterTimer = setTimeout(() => {
        this.$emit('action', 'filter', filter)
      }, 300)
    },

    clearFilter (event) {
      const filterElement = event.target.previousElementSibling
      if (filterElement.value) {
        filterElement.value = ''
        filterElement.classList.remove('active')
        this.$emit('action', 'filter', '')
      }
    },

    filters (event, column) {
      let value = event.target.value,
          name = column.name
      if (column.filter?.type === 'date' || column.filter?.type === 'datetime') {
        value = [
          event.target.parentElement.firstElementChild['value'],
          event.target.parentElement.lastElementChild['value']
        ]

        if (value[0] === '' || value[1] === '') {
          value = ''
        }
      }

      if (this.propUrl) {
        const query = Object.assign({}, this.currentRoute['query'] || {})
        if (value !== '') {
          query[name] = value
        } else {
          delete query[name]
        }

        delete query['page']

        this.get(query)

        this.$emit(
            'action',
            'pushRouter',
            { query }
        )
      }

      //this.$emit('action', 'filters', value, name)
    },

    updateModelValue () {
      this.$emit('update:modelValue', ...arguments)

      if (this.rerender) {
        // Обновляем весь компонент
        this.renderComponent = false
        this.$nextTick(() => {
          this.renderComponent = true
        })
      }
    },

    sortable () {
      this.$emit('action', 'sortable', ...arguments)
    },

    selectRow (item, data, event) {
      if (!event.ctrlKey) {
        data.map((i) => {
          if (i.id !== item.id || i.key !== item.key) {
            i.active = false
          }
        })
      }

      item.active = !item.active

      this.$emit('action', 'panelSelectRow', data.filter(i => i.active))
    }
  }
}
</script>

<style scoped>
.panel {
  @apply flex flex-col w-full bg-white dark:bg-cms-700
}
.data {
  @apply grow overflow-auto
}
.data thead {
  @apply sticky top-0
}
.data thead th {
  @apply p-0
}
.data thead tr.columns th > div {
  @apply relative inline-flex px-3 py-2 w-full h-full justify-center items-center
}
.data thead tr.columns th:first-child > div {
  @apply pl-6
}
.data thead tr.columns th:last-child > div {
  @apply pr-6
}
.data thead tr.columns th.sortable > div {
  @apply pr-8 hover:bg-slate-200 dark:hover:bg-cms-900 cursor-pointer
}
.data thead th .splitter {
  @apply absolute z-10 top-0 -right-[2px] bottom-0 w-[2px] bg-gray-200 dark:bg-gray-700 hover:cursor-col-resize hidden md:block
}
.data thead th .splitter::before {
  @apply relative block h-full w-2 -ml-1 cursor-col-resize;
  content: "";
}
.data thead th.column-resize .splitter {
  @apply h-full bg-blue-500
}
.data thead .sort {
  @apply absolute right-2 cursor-pointer
}
.data thead th .active .sort, .data thead th:hover .sort {
  @apply text-blue-500
}
.data thead th {
  @apply first:pl-4 last:pr-4
}
.data thead tr.filters th > div {
  @apply -m-[1px]
}
.data thead tr.filters select, .data thead tr.filters input {
  @apply focus:z-10 relative
}
.data tbody tr.disabled {
  @apply even:bg-rose-600/10 bg-rose-600/20 hover:bg-rose-600/30 dark:hover:bg-rose-600/30
}
.data .actions {
  @apply text-center whitespace-nowrap
}
.data .actions i {
  @apply mx-1 cursor-pointer
}
.data .data-mask {
  @apply hidden absolute z-10 left-0 top-0 right-0 bottom-0
}
.data-resize .data .data-mask {
  @apply block cursor-col-resize
}
.filter {
  @apply grow md:grow-0 relative inline-flex items-center
}
.filter input {
  @apply h-8 pl-2 pr-5 w-full md:w-96 text-sm
}
.filter input + i {
  @apply absolute right-2 cursor-pointer opacity-0 invisible text-gray-300 dark:text-gray-500 hover:text-rose-500 dark:hover:text-rose-600 transition
}
.filter input.active + i {
  @apply opacity-100 visible
}
</style>

<style>
.panel .data tbody tr td .icon {
  @apply relative
}
.panel .data tbody tr td .icon > i.fa-lock {
  @apply absolute -right-0.5 -bottom-0.5 text-rose-600 text-xs
}
.panel .draggable-handle {
  @apply cursor-grab
}
.panel .sortable-chosen .draggable-handle {
  @apply cursor-grabbing
}
</style>
