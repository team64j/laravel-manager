<script setup>
import { compile, computed, h } from 'vue'

const props = defineProps(['data', 'meta', 'layout', 'loaderDelay', 'currentRoute'])
const emit = defineEmits(['action', 'update:modelValue'])

const loaderDelay = props.loaderDelay || 0
let renderLayout = computed(init)

function updateModelValue (value, instance) {
  if (!instance) {
    return
  }

  let key
  if (typeof instance === 'object') {
    key = instance._.vnode.key
  } else {
    key = instance
  }
  props.data[key] = value
  emit('update:modelValue', ...arguments)
}

function action () {
  emit('action', ...arguments)
}

function findData (keys, data) {
  let obj = {}

  data = data || props

  keys.forEach((key, index) => {
    if (data[key]) {
      if (keys[index + 1]) {
        keys.shift()
        obj = findData(keys, data[key])
      } else {
        obj = data[key]
      }
    }
  })

  return obj
}

function initData (data, setAction) {
  data = data || setLayoutData()

  if (!data) {
    return
  }

  if (data.component) {
    return createComponent(data, setAction)
  }

  if (typeof data === 'string') {
    data = data.replace(/href=/g, 'to=').replace(/<a(.*?)>/g, '<router-link$1>').replace(/(<\/a>)/g, '</router-link>')
    return h(compile(data))
  }

  if (typeof data === 'object') {
    let obj
    if (Array.isArray(data)) {
      obj = []
      data.forEach(i => obj.push(initData(i, setAction)))
    } else {
      obj = Object.create(null)
      for (let i in data) {
        const slots = initData(data[i])
        obj[i] = () => slots
      }
    }
    return obj
  }

  return data
}

function createComponent (data, setAction) {
  const component = Vue._context.components[data.component]
  const attrs = data.attrs || {}
  let slots

  if (!component) {
    return
  }

  attrs.key = data.model || data.component
  attrs['onUpdate:modelValue'] = updateModelValue
  attrs.modelValue = attrs.value

  if (component.props?.['currentRoute']) {
    attrs.currentRoute = props.currentRoute
  }

  if (setAction || component.props?.['modelValue']) {
    attrs.onAction = action
  }

  if (props.data?.[attrs?.key] !== undefined) {
    attrs.modelValue = props.data[attrs.key]
  }

  if (data.slots) {
    slots = initData(data.slots)
  }

  return h(component, attrs, slots)
}

function setLayoutData (data) {
  data = data || props.layout

  for (let i in data) {
    if (typeof data[i]?.data === 'string') {
      const keys = data[i].data.split('.')

      if (!data[i].attrs) {
        data[i].attrs = {}
      }

      data[i].attrs.data = findData(keys)
      data[i].attrs.onAction = action
    } else if (data[i]?.slots) {
      data[i].slots = setLayoutData(data[i].slots)
    } else if (Array.isArray(data[i])) {
      data[i] = setLayoutData(data[i])
    }
  }

  return data
}

function init () {
  return h('div', { class: 'layout' }, initData(null, true))
}
</script>

<template>
  <component v-if="layout" :is="renderLayout"/>
  <div v-else class="flex items-center justify-center">
    <loader :delay="loaderDelay"/>
  </div>
</template>
