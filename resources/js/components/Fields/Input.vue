<template>
  <div v-if="label" class="w-full mb-3" :class="$props.class">
    <div class="mb-1">
      <label :for="id" class="font-bold cursor-pointer">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
        <help-icon v-if="help" :data="help"/>
      </label>
      <span v-if="requiredText" class="text-rose-500 ml-3 text-sm font-normal">{{ requiredText }}</span>
      <slot name="label"/>
    </div>
    <div :class="{ 'input-number': type === 'number' }">
      <input :type="type" :id="id" v-model="model" class="block w-full px-3 py-1 rounded" :class="inputClass"
             :readonly="inputReadonly"
             @mousedown="onMousedown">
    </div>
    <div class="opacity-75 text-sm">{{ description }}</div>
    <slot name="item"/>
  </div>
  <div v-else :class="{ 'input-number': type === 'number' }">
    <input :type="type" v-model="model" class="block w-full px-3 py-1 rounded" :class="inputClass"
           :readonly="inputReadonly"
           @mousedown="onMousedown">
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </div>
</template>

<script>
export default {
  name: 'FieldInput',

  emits: ['action', 'update:modelValue'],

  props: {
    modelValue: {
      type: [null, Object, String, Number, Boolean],
      default: null
    },
    value: {
      type: [String, Number, Array]
    },
    class: [Array, Object, String],
    label: String,
    help: String,
    description: String,
    required: Boolean,
    requiredText: String,
    inputClass: [Array, Object, String],
    inputReadonly: Boolean,
    data: {
      type: [Object, Array]
    },
    type: {
      type: String,
      default: 'text',
      validator: (prop) => ['text', 'number', 'password', 'email', 'tel', 'date', 'datetime-local'].includes(prop)
    }
  },

  computed: {
    model: {
      get () {
        return this.value || this.modelValue
      },
      set (value) {
        this.$emit('update:modelValue', value, this)
      }
    }
  },

  data () {
    return {
      id: `field-${this.type}-${this.$.uid}`
    }
  },

  methods: {
    onMousedown (event) {
      this.$emit('action', 'mousedown:input', event, this)
    }
  }
}
</script>

<style scoped>
.input-number {
  @apply relative
}
.input-number::before, .input-number::after {
  @apply absolute hidden md:block right-[1px] w-9 h-1/2 bg-white dark:bg-cms-800 text-xs text-center text-gray-400 dark:text-gray-600 pointer-events-none;
}
.input-number::before {
  @apply top-[1px] rounded-tr;
  content: "\25B2";
}
.input-number::after {
  @apply bottom-[1px] rounded-br;
  content: "\25BC";
}
input[type="number"] {
  @apply pr-1
}
</style>
