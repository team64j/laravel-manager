<template>
  <template v-if="columns && columns.length">
    <td v-for="(column, key) in columns" :style="column.style">
      <template v-if="column.actions">
        <div class="whitespace-nowrap">
          <help-icon
              class="cursor-pointer"
              v-for="(a, k) in column.actions"
              :icon="[a.values ? a.values[item[k]]?.icon : a.icon]"
              :fit="a?.values?.[item[k]]?.helpFit ?? a.helpFit"
              :no-opacity="a?.values?.[item[k]]?.noOpacity ?? a.noOpacity"
              :title="a.values ? a.values[item[k]]?.title : a.title"
              :data="a?.values?.[item[k]]?.help ?? a.help"
              @click.prevent="action(k, item, a.values ? item[k] : a)"/>
        </div>
      </template>
      <div v-else-if="item[column.name + `.html`] !== undefined" v-html="item[column.name + `.html`]"/>
      <div v-else-if="column.icon" v-html="column.icon"/>
      <template v-else-if="column.values && (column.values?.[item[column.name]] !== undefined || column.values?.[item[column.key]] !== undefined)">
        <div v-html="column.values[item[column.name]] ?? column.values?.[item[column.key]]"/>
      </template>
      <template v-else-if="item[column.name]?.component">
        <panel-item-component
            :key="column.name"
            :model-value="modelValue(item, column)"
            :props="item[column.name]"
            :category="category"
            :item="item"
            @action="action"
            @update:modelValue="updateModelValue"/>
      </template>
      <template v-else-if="column?.component">
        <panel-item-component
            :key="column.name + item.id"
            :model-value="modelValue(item, column)"
            :props="column"
            :category="category"
            :item="item"
            @action="action"
            @update:modelValue="updateModelValue"/>
      </template>
      <div v-else v-bind="column?.attrs">
        {{ value(item, column) }}
        <help-icon v-if="item[column.name + `.help`]" :data="item[column.name + `.help`]"/>
      </div>
    </td>
  </template>

  <template v-else>
    <td v-for="(value, key) in item" :key="key">
      <div v-if="key.substring(key.length - 5) === '.html'" v-html="value"/>
      <template v-else>
        {{ value }}
      </template>
    </td>
  </template>
</template>

<script>
import PanelItemComponent from './PanelItemComponent'

export default {
  name: 'PanelCell',
  components: { PanelItemComponent },

  emits: ['action', 'update:modelValue'],

  props: {
    columns: {
      type: Array
    },
    category: {
      type: Object
    },
    item: {
      type: Object
    },
    data: {
      type: Object
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

    updateModelValue () {
      this.$emit('update:modelValue', ...arguments)
    },

    modelValue (item, column) {
      if (column?.component) {
        return item[column.name]
      }

      const columnData = item[column.name]?.data
      const columnModel = item[column.name].model

      return columnModel && (columnData ?? this.data)[columnModel]
    },

    value (item, column) {
      let key = column.key ?? column.name

      return item[key + `.name`] ??
          item[key] ??
          (/./.test(key) ? this.findValue(key.split('.'), item) : '')
    },

    findValue (keys, data) {
      if (!data) {
        return
      }

      let value = ''

      keys.forEach((key, index) => {
        if (data[key] !== undefined) {
          if (keys[index + 1]) {
            keys.shift()
            value = this.findValue(keys, data[key])
          } else {
            value = data[key]
          }
        }
      })

      return value
    }
  }
}
</script>

<style scoped>
td {
  @apply px-3 py-1 first:pl-6 last:pr-6
}
</style>
