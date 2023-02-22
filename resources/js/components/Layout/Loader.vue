<template>
  <div v-if="timer" class="loader animate-ping"/>
</template>

<script>
export default {
  name: 'Loader',

  props: {
    delay: {
      type: Number,
      default: 1000
    }
  },

  data () {
    return {
      timer: false
    }
  },

  created () {
    setTimeout(() => this.timer = true, this.delay)
  }
}
</script>

<style scoped>
.loader {
  @apply w-48 h-48
}
.loader::before {
  content: "";
  display: block;
  position: absolute;
  z-index: 1;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  animation: rotate 2s linear infinite;
  box-shadow: 5px 5px 0 0 rgb(234 132 82), 14px -7px 0 0 rgb(111 163 219 / 70%), -7px 11px 0 0 rgb(112 193 92 / 74%), -11px -7px 0 0 rgb(147 205 99 / 78%);
}
</style>
