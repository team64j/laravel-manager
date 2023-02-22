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
    <div class="flex">
      <input type="text" :id="id" v-model="model" class="block w-full px-3 py-1 rounded" :class="inputClass"
             :readonly="inputReadonly"
             @mousedown="onMousedown">
      <button type="button" class="btn-sm btn-gray ml-1" @click="open">{{ $root.lang('insert') }}</button>
    </div>
    <div class="opacity-75 text-sm">{{ description }}</div>
    <slot name="item"/>
  </div>
  <div v-else>
    <div class="flex">
      <input type="text" v-model="model" class="block w-full px-3 py-1 rounded" :class="inputClass"
             :readonly="inputReadonly"
             @mousedown="onMousedown">
      <button type="button" class="btn-sm btn-gray ml-1" @click="open">{{ $root.lang('insert') }}</button>
    </div>
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </div>
</template>

<script>
export default {
  name: 'FieldFile',

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
      default: 'file',
      validator: (prop) => ['file', 'image'].includes(prop)
    }
  },

  computed: {
    model: {
      get () {
        let value = this.value || this.modelValue

        if (Array.isArray(value)) {
          const newValue = []
          value.map(i => {
            newValue.push(this.$root.config('rb_base_url') + decodeURIComponent(escape(window.atob(i.key))))
          })

          value = newValue.join(',')
        }

        return value
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
    },

    open () {
      this.$root.$refs.window.open({
        currentRoute: this.$router.resolve({ name: 'Files' }),
        context: this
      })
    }
  }
}
</script>

<style scoped>

</style>
