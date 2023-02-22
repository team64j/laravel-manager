<template>
  <div class="w-full mb-3" :class="$props.class">
    <label v-if="label" for="" class="block font-bold mb-1">
      {{ label }}
      <help-icon v-if="help" :data="help"/>
    </label>
    <div class="relative editor">
      <div class="settings">
        <i class="fas fa-expand fa-compress fullscreen" @click="fullscreen"/>
        <i class="fa fa-gear select" v-if="config.length > 1">
          <span class="menu">
            <span v-for="i in config" :class="{ 'active': i.active }" @click="select"
                  :data-component="i.component">{{ i.name }}</span>
          </span>
        </i>
      </div>
      <component v-model="model" :is="component" :extensions="extensions" :style="{ minHeight: height }"
                 class="w-full"/>
    </div>
    <div v-if="description" v-html="description" class="opacity-75 text-sm"/>
  </div>
</template>

<script>
import { h } from 'vue'
import { Codemirror } from 'vue-codemirror'
import { oneDark } from '@codemirror/theme-one-dark'
import { javascript } from '@codemirror/lang-javascript'
import { markdown } from '@codemirror/lang-markdown'
import { json } from '@codemirror/lang-json'
import { html } from '@codemirror/lang-html'
import { php } from '@codemirror/lang-php'
import { xml } from '@codemirror/lang-xml'
import { css } from '@codemirror/lang-css'
import { vue } from '@codemirror/lang-vue'
import { sql } from '@codemirror/lang-sql'

const languages = {
  javascript,
  markdown,
  html,
  json,
  css,
  xml,
  php,
  vue,
  sql
}

export default {
  name: 'CodeEditor',

  components: { Codemirror },

  props: {
    modelValue: {
      type: [String, Object, Number]
    },
    config: {
      type: Array,
      default: [{ component: 'Codemirror', name: 'Codemirror' }]
    },
    rows: {
      type: [String, Number],
      default: 3
    },
    class: [Array, Object, String],
    label: String,
    help: String,
    description: String
  },

  data () {
    return {
      currentConfig: null,
      defaultConfig: [
        {
          active: true,
          component: 'Textarea',
          name: this.$root.lang('none')
        },
        {
          component: 'Codemirror',
          name: 'Codemirror'
        }
      ],
      dark: (this.$root['dark'] || this.currentConfig?.dark) || false
    }
  },

  computed: {
    component () {
      if (!this.currentConfig) {
        return
      }

      if (this.currentConfig.component === 'Textarea') {
        this.currentConfig.component = () => h('textarea', {
          value: this.model,
          rows: this.rows,
          class: 'block w-full px-3 py-1 rounded resize-none',
          onInput: event => {
            this.$emit('update:modelValue', event.target.value, this)
          }
        })
      }

      return this.currentConfig.component
    },
    model: {
      get () {
        return this.modelValue
      },
      set (value) {
        this.$emit('update:modelValue', value, this)
      }
    },
    extensions () {
      if (!this.currentConfig) {
        return
      }

      const extensions = []

      if (languages[this.currentConfig?.lang]) {
        extensions.push(languages[this.currentConfig.lang]())
      }

      if (this.dark || this.currentConfig?.dark) {
        extensions.push(oneDark)
      }

      return extensions
    },
    height () {
      return (this.rows * 1.53115) + 'rem'
    }
  },

  watch: {
    '$root.dark' (dark) {
      this.dark = dark || this.currentConfig?.dark || false
    }
  },

  created () {
    this.currentConfig = this.config.length > 1
        ? this.config.filter(i => i.active)[0] || this.defaultConfig[0]
        : this.config[0]
  },

  methods: {
    select (event) {
      this.config.map(i => {
        if (i.component === event.currentTarget.dataset.component) {
          i.active = true
          this.currentConfig = { ...i }
        } else {
          i.active = false
        }
      })
    },

    fullscreen () {
      const target = this.$el.querySelector('.fullscreen')

      if (target.classList.contains('fa-expand')) {
        target.classList.remove('fa-expand')
      } else {
        target.classList.add('fa-expand')
      }

      this.$el.querySelector('.editor').classList.toggle('editor-fullscreen')
    }
  }
}
</script>

<style scoped>
.settings {
  @apply absolute z-10 top-1.5 right-2 w-5
}
.settings i {
  @apply relative mb-2 opacity-80 hover:opacity-100
}
.settings i:hover > * {
  @apply opacity-100 visible
}
.settings .menu {
  @apply block opacity-0 invisible absolute right-0 top-full py-1 rounded shadow-md font-sans font-normal text-base text-white bg-gray-800 dark:text-gray-800 dark:bg-white transition
}
.settings .menu span:hover, .settings .menu span.active {
  @apply bg-blue-600 text-white
}
.settings .menu span {
  @apply block px-4 py-1 cursor-pointer hover:bg-blue-500
}
.settings .fullscreen {
  @apply cursor-pointer
}
.editor-fullscreen {
  @apply fixed z-50 left-0 top-0 right-0 bottom-0 bg-slate-100 dark:bg-cms-800;
  z-index: 90099;
}
.editor-fullscreen > textarea, .editor-fullscreen .v-codemirror {
  @apply h-full
}
</style>
