<template>
  <div class="actions-buttons">

    <button
        v-if="~data.indexOf('cancel')"
        type="button"
        class="btn-sm btn-light mx-0.5"
        :title="$root.lang('cancel')"
        @click="cancel">
      <i class="fa fa-reply fa-fw"/>
      <span>{{ $root.lang('cancel') }}</span>
    </button>

    <button
        v-if="~data.indexOf('delete')"
        type="button"
        class="btn-sm btn-light mx-0.5"
        :class="this.class['delete']"
        :title="lang['delete'] || $root.lang('delete')"
        @click="this.delete">
      <i class="fa fa-trash-alt fa-fw"/>
      <span>{{ lang['delete'] || $root.lang('delete') }}</span>
    </button>

    <button
        v-if="~data.indexOf('clear')"
        type="button"
        class="btn-sm btn-red mx-0.5"
        :title="$root.lang('clear_log')"
        @click="this.clear">
      <i class="fa fa-trash-alt fa-fw"/>
      <span>{{ $root.lang('clear_log') }}</span>
    </button>

    <button
        v-if="~data.indexOf('restore')"
        type="button"
        class="btn-sm btn-light mx-0.5"
        :title="$root.lang('undelete_resource')"
        @click="this.restore">
      <i class="fa fa-undo fa-fw"/>
      <span>{{ $root.lang('undelete_resource') }}</span>
    </button>

    <button
        v-if="~data.indexOf('copy')"
        type="button"
        class="btn-sm btn-light mx-0.5"
        :title="$root.lang('duplicate')"
        @click="copy">
      <i class="fa fa-copy fa-fw"/>
      <span>{{ $root.lang('duplicate') }}</span>
    </button>

    <button
        v-if="~data.indexOf('view')"
        type="button"
        class="btn-sm btn-light mx-0.5"
        :title="$root.lang('view')"
        @click="view">
      <i class="fa fa-eye fa-fw"/>
      <span>{{ $root.lang('view') }}</span>
    </button>

    <button
        v-if="~data.indexOf('save')"
        type="button"
        class="btn-sm btn-green mx-0.5 relative"
        :title="$root.lang('save')"
        @click="save">
      <i class="fa fa-save fa-fw"/>
      <span>{{ $root.lang('save') }}</span>
      <i v-if="saving" class="btn-sm btn-green cursor-progress absolute flex items-center justify-center left-0 top-0 h-full w-full z-10"
         @click.prevent.stop="() => false">
        <loader-icon/>
      </i>
    </button>

    <div v-else-if="~data.indexOf('saveAnd')" class="flex relative">
      <button v-for="i in saveButtons.filter(j => j.stay === stay)" :title="$root.lang('save') + ' + ' + i.title"
              @click="save($event, i.stay)"
              class="btn-sm btn-green rounded-r-none">
        <i class="fa fa-save fa-fw"/><i class="fa fa-plus fa-fw"/><i :class="i.icon"/>
        <span>{{ $root.lang('save') }} + {{ i.title }}</span>
        <i v-if="saving" class="btn-sm btn-green cursor-progress absolute flex items-center justify-center left-0 top-0 h-full w-full z-10"
           @click.prevent.stop="() => false">
          <loader-icon/>
        </i>
      </button>

      <button type="button" class="btn-sm btn-green rounded-l-none" @click="isToggle=!isToggle" @blur="isToggle=false">
        <i class="fa-solid fa-angle-down fa-fw toggle transition" :class="{ 'transform rotate-180': isToggle }"/>
      </button>

      <div v-if="isToggle" class="save-buttons">
        <button v-for="i in saveButtons.filter(j => j.stay !== stay)" :title="$root.lang('save') + ' + ' + i.title"
                @mousedown.prevent="save($event, i.stay)"
                class="btn-sm btn-light border-t">
          <i :class="i.icon"/>
          <span>{{ i.title }}</span>
        </button>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: 'LayoutActionsButtons',
  props: {
    data: {
      type: Array,
      default: ['cancel', 'delete', 'restore', 'copy', 'view', 'save']
    },
    lang: {
      type: Object,
      default: {}
    },
    class: {
      type: [Object, String],
      default: {}
    },
  },

  data () {
    return {
      isToggle: false,
      stay: this.$root.config('stay') || 0,
      saveButtons: [
        {
          stay: 0,
          icon: 'fa fa-reply fa-fw',
          title: this.$root.lang('close')
        },
        {
          stay: 1,
          icon: 'fa fa-copy fa-fw',
          title: this.$root.lang('stay_new')
        },
        {
          stay: 2,
          icon: 'fa fa-pencil fa-fw',
          title: this.$root.lang('stay')
        }
      ]
    }
  },

  computed: {
    saving () {
      return this.$store.getters['get']('saving')
    }
  },

  methods: {
    cancel (event) {
      this.$emit('action', 'cancel', event, 1, 2, 3)
    },

    delete (event) {
      this.$emit('action', 'delete', event)
    },

    clear (event) {
      this.$emit('action', 'clear', event)
    },

    restore (event) {
      this.$emit('action', 'restore', event)
    },

    save (event, stay) {
      this.isToggle = false
      this.stay = stay
      this.$store.dispatch('set', { stay: stay, saving: true })
      this.$emit('action', 'save', stay)
    },

    copy (event) {
      this.$emit('action', 'copy', event)
    },

    view (event) {
      this.$emit('action', 'view', event)
    }
  }
}
</script>

<style scoped>
.actions-buttons {
  @apply absolute flex rounded-b bg-gray-500/5 p-2 right-4 z-50
}
.actions-buttons > button, .actions-buttons > div {
  @apply inline-flex mx-0.5
}
button {
  @apply shadow
}
button > i.fa:not(.toggle) {
  @apply md:hidden
}
button span {
  @apply hidden md:inline-block px-1
}
.save-buttons {
  @apply absolute left-0 top-full mt-0.5 w-full flex flex-col opacity-100 visible transition
}
.save-buttons button {
  @apply border-t-gray-200 dark:border-t-gray-700 rounded-none first:rounded-t last:rounded-b
}
</style>
