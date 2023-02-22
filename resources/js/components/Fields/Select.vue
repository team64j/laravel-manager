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
    <div class="relative">
      <select v-model="model" :id="id" class="block w-full pl-3 pr-8 py-1 rounded" @mousedown="onMousedown"
              @change="change" @focus="focus">

        <template v-for="i in options">

          <optgroup v-if="i.data" :label="i.name">
            <option v-for="j in i.data" :value="j.key" :selected="j.selected">{{ j.value }}</option>
          </optgroup>

          <option v-else :value="i.key" :selected="i.selected">{{ i.value }}</option>

        </template>

      </select>
      <template v-if="itemNew">
        <input type="text" class="block w-full px-3 py-1 rounded" @input="input"/>
        <i class="fa fa-circle-xmark" @click="clear"/>
      </template>
    </div>
  </div>
  <template v-else>
    <select v-model="model" class="block w-full pl-3 pr-8 py-1 rounded" @mousedown="onMousedown">
      <template v-for="i in options">
        <optgroup v-if="i.data" :label="i.name">
          <option v-for="j in i.data" :value="j.key" :selected="j.selected">{{ j.value }}</option>
        </optgroup>
        <option v-else :value="i.key" :selected="i.selected">{{ i.value }}</option>
      </template>
    </select>
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </template>
</template>

<script>
export default {
  name: 'FieldSelect',

  emits: ['action', 'update:modelValue'],

  props: {
    modelValue: [null, Object, String, Number, Boolean],
    value: [String, Number],
    class: [Array, Object, String],
    label: String,
    help: String,
    description: String,
    required: Boolean,
    data: {
      type: [Object, Array]
    },
    url: String,
    itemNew: String
  },

  data () {
    return {
      id: `field-select-${this.$.uid}`,
      options: this.data || []
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

  methods: {
    onMousedown (event) {
      if (!this.url) {
        return
      }

      event.target.dataset.value = event.target.value

      axios.get(this.url, {
        params: {
          selected: event.target.value
        }
      }).then(r => {
        this.options = r.data.data
        this.$emit('action', 'mousedown:select', event, this)
      })
    },

    change (event) {
      const target = event.target

      if (target.value === this.itemNew) {
        target.value = target.dataset.value
        this.$emit('update:modelValue', target.value, this)

        target.parentElement.classList.add('editable')
        target.nextElementSibling.focus()
      }
    },

    focus (event) {
      if (event.target.parentElement.classList.contains('editable')) {
        event.target.nextElementSibling.focus()
      }
    },

    input (event) {
      if (event.target.value.length) {
        event.target.classList.add('active')
      } else {
        event.target.classList.remove('active')
      }
      this.$emit('update:modelValue', event.target.value, this.itemNew)
    },

    clear (event) {
      const input = event.target.parentElement.querySelector('input'),
          select = event.target.parentElement.querySelector('select')
      input.value = ''
      input.classList.remove('active')
      event.target.parentElement.classList.remove('editable')
      select.value = select.dataset.value
      this.$emit('update:modelValue', select.value, this)
      this.$emit('update:modelValue', '', this.itemNew)
    }
  }
}
</script>

<style scoped>
select + input {
  @apply hidden absolute left-0 top-0 pr-8
}
select + input + i {
  @apply hidden
}
.editable select + input {
  @apply block
}
.editable select {
  @apply opacity-0
}
.editable select + input + i {
  @apply absolute block right-0 top-0 my-2.5 mx-3 cursor-pointer text-gray-300 dark:text-gray-500 hover:text-rose-500 dark:hover:text-rose-600 transition
}
</style>
