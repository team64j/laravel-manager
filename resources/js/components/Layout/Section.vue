<template>
  <div class="section flex flex-row flex-wrap grow-0 w-full p-4 mb-4 rounded shadow">
    <div v-if="label" class="header relative flex flex-wrap items-center w-full transition"
         :class="{ 'cursor-pointer hover:text-blue-500': propExpanded !== null}"
         @click="click">
      <div class="grow">
        <i v-if="icon" :class="icon" class="mr-2"/>
        <strong>{{ label }}</strong>
      </div>
      <div v-if="propExpanded !== null" class="absolute right-0">
        <i class="fa fa-angle-down transition" :class="{ 'rotate-180' : !propExpanded }"/>
      </div>
    </div>
    <div v-show="propExpanded ?? true" class="flex flex-wrap w-full">
      <div class="mt-3 pb-3 border-t w-full"/>
      <slot name="default"/>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LayoutSection',

  props: {
    currentRoute: Object,
    label: String,
    icon: String,
    expanded: {
      type: Boolean,
      default: null
    }
  },

  data () {
    return {
      propExpanded: this.expanded ?? null
    }
  },

  methods: {
    click () {
      if (this.propExpanded !== null) {
        this.propExpanded=!this.propExpanded
      }
    }
  }
}
</script>
