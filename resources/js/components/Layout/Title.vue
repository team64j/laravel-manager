<template>
  <div class="w-full py-4 px-5">
    <h1 class="title h4 m-0">
      <i v-if="icon || $route.meta.icon" class="mr-2 w-6 text-center text-lg text-gray-600 dark:text-gray-400"
         :class="icon || $route.meta.icon"/>
      <span v-html="data || modelValue || title || $route.meta.title" class="text-2xl font-bold"/>
      <span v-if="cId" class="ml-2">
        ({{ cId }})
      </span>
      <i v-if="help" class="fa fa-question-circle ml-3 text-sm text-gray-400 hover:opacity-75 cursor-pointer"
         @click="show=!show"/>
    </h1>
    <div v-if="help" v-show="show" class="alert alert-blue mt-3" v-html="help"/>
  </div>
</template>

<script>
export default {
  name: 'LayoutTitle',

  props: {
    id: [String, Number],
    data: String,
    modelValue: String,
    title: String,
    icon: String,
    help: String
  },

  data () {
    return {
      show: false
    }
  },

  computed: {
    cId () {
      if (this.id !== undefined) {
        return this.id
      }

      if (this.$route.params.id) {
        return this.$route.params.id
      }
    }
  }
}
</script>
