<template>
  <div v-if="label" class="w-full mb-3" :class="$props.class">
    <div class="mb-1">
      <label :for="id" class="font-bold cursor-pointer">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
        <help-icon v-if="help" :data="help"/>
      </label>
      <slot name="label"/>
    </div>
    <textarea type="text" :id="id" v-model="model" class="block w-full px-3 py-1 rounded resize-none" :rows="rows"/>
  </div>
  <template v-else>
    <textarea type="text" v-model="model" class="block w-full px-3 py-1 rounded resize-none" :rows="rows"/>
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </template>
</template>

<script>
export default {
  name: 'FieldTextarea',

  emits: ['action', 'update:modelValue'],

  props: {
    modelValue: {
      type: [null, Object, String, Number, Boolean],
      default: ''
    },
    value: [String, Number],
    class: [Array, Object, String],
    label: String,
    help: String,
    description: String,
    required: Boolean,
    data: {
      type: [Object, Array]
    },
    rows: {
      type: Number,
      default: 2
    }
  },

  computed: {
    model: {
      get () {
        return this.modelValue
      },
      set (value) {
        this.$emit('update:modelValue', value, this)
      }
    }
  },

  data () {
    return {
      id: 'field-textarea-' + this.$.uid
    }
  }
}
</script>

<style scoped>

</style>
