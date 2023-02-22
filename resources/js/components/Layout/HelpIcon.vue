<template>
  <i :class="[
      icon,
      fit ? 'help-fit-content' : '',
      noOpacity ? 'no-opacity' : '',
      data ? 'cursor-help overflow-hidden' : ''
      ]"
     class="help"
     :title="title"
     @mouseenter="mouseenter"
     @mouseout="mouseleave"
     @click.prevent="click">
    <i v-if="iconInner" :class="iconInner"/>
    <span v-if="data" v-html="data" @click.stop.prevent="() => {}"/>
  </i>
</template>

<script>
export default {
  name: 'HelpIcon',

  props: {
    data: {
      type: String
    },
    title: {
      type: String
    },
    icon: {
      default: 'fa fa-question-circle'
    },
    iconInner: {
      default: ''
    },
    fit: {
      default: ''
    },
    noOpacity: {
      default: ''
    }
  },

  data () {
    this.timer = 0

    return {}
  },

  methods: {
    mouseenter () {
      if (!this.data || this.$el.querySelector(':scope > span:hover')) {
        return
      }

      this.timer = setTimeout(() => {
        this.$el.classList.add('hover')
        const position = this.$el.getBoundingClientRect(),
            help = this.$el.querySelector(':scope > span')

        let left = position.left + 16,
            top = position.top + 16

        if (left + help.offsetWidth > window.innerWidth) {
          left = position.left - help.offsetWidth

          if (position.left - help.offsetWidth < 0) {
            left = (window.innerWidth - help.offsetWidth) - 16
          }
        }

        if (top + help.offsetHeight > window.innerHeight) {
          top = position.top - help.offsetHeight
        }

        help.style.left = left + 'px'
        help.style.top = top + 'px'
      }, 10)
    },

    mouseleave () {
      if (!this.data) {
        return
      }

      setTimeout(() => {
        clearTimeout(this.timer)
        if (!this.$el.querySelector(':scope > span:hover')) {
          this.$el.classList.remove('hover')
        }
      }, 250)
    },

    click () {
      if (!this.data) {
        return
      }

      this.$el.classList.toggle('hover')
    }
  }
}
</script>

<style scoped>
i.help {
  @apply ml-2 inline-flex justify-center relative
}
i.help.hover {
  @apply overflow-visible
}
i.help::before {
  @apply opacity-20
}
i.help.hover::before {
  @apply opacity-60
}
i.help.no-opacity::before {
  @apply opacity-100
}
i.help > span {
  @apply fixed min-w-[18rem] max-w-[24rem]
}
i.help.help-fit-content > span {
  @apply min-w-fit
}
i.help.help-right > span {
  @apply left-auto right-0
}
</style>

<style>
i.help > i {
  @apply absolute text-xs text-rose-500 bottom-0 right-0
}
i.help > span {
  @apply block absolute z-20 left-0 top-0 p-4 text-sm text-left font-normal font-sans rounded text-white bg-gray-800 dark:text-gray-800 dark:bg-white shadow-lg opacity-0 invisible cursor-default transition
}
i.help.hover > span {
  @apply opacity-90 visible
}
i.help > span .badge {
  @apply absolute top-4 right-4 px-2 h-5 text-center font-medium not-italic rounded-full text-gray-900 bg-amber-400
}
</style>
