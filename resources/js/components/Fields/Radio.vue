<template>
  <div v-if="label" class="w-full mb-3" :class="$props.class">
    <div v-if="!data">
      <label class="inline-flex items-center " :class="[ disabled ? 'cursor-no-drop' : 'cursor-pointer' ]">
        <input type="radio"
               class="mr-2"
               v-model="model"
               :value="value"
               :true-value="trueValue"
               :false-value="falseValue"
               :disabled="disabled">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
        <help-icon v-if="help" :data="help"/>
      </label>
      <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
    </div>
    <template v-else>
      <label for="" class="block font-bold mb-1">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
        <help-icon v-if="help" :data="help"/>
      </label>
      <div v-for="(i, k) in data">
        <label :key="k" class="inline-flex items-center cursor-pointer">
          <input type="radio"
                 class="mr-2"
                 v-model="model"
                 :value="i.key"
                 :true-value="i.key"
                 :false-value="falseValue">
          <span>{{ i.value }}</span>
        </label>
      </div>
    </template>
  </div>
  <template v-else>
    <input type="radio"
           v-model="model"
           :value="value"
           :true-value="trueValue"
           :false-value="falseValue">
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </template>
</template>

<script>
export default {
  name: 'FieldRadio',

  emits: ['action', 'update:modelValue'],

  props: {
    modelValue: { default: true },
    value: { default: true },
    class: [Array, Object, String],
    label: String,
    help: String,
    description: String,
    required: Boolean,
    disabled: Boolean,
    data: [Object, Array],
    trueValue: { default: true },
    falseValue: { default: false }
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
      id: 'field-radio-' + this.$.uid
    }
  }
}
</script>

<style scoped>

</style>
