<template>
  <div class="flex flex-col">

    <actions-buttons :data="['delete', 'cancel']" @action="action"/>

    <title-component/>

    <div class="py-2 bg-white dark:bg-cms-700" v-if="data">
      <div class="data data-event-log">
        <table>
          <thead>
          <tr>
            <th colspan="4">{{ data.source }} - {{ $root.lang('eventlog_viewer') }}</th>
          </tr>
          <tr>
            <th>{{ $root.lang('event_id') }}</th>
            <th>{{ $root.lang('source') }}</th>
            <th>{{ $root.lang('date') }}</th>
            <th>{{ $root.lang('user') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr class="text-center">
            <td>{{ data.eventid }}</td>
            <td>{{ data.source }}</td>
            <td>{{ data.createdon }}</td>
            <td>{{ data?.users?.username || '-' }}</td>
          </tr>
          </tbody>
        </table>
      </div>

      <div v-html="data.description" class="data data-event-log"/>

    </div>

    <div v-else class="flex items-center justify-center grow">
      <loader/>
    </div>
  </div>
</template>

<script>
import ActionsButtons from '@/components/Layout/ActionsButtons.vue'
import TitleComponent from '@/components/Layout/Title.vue'

export default {
  name: 'EventLogPage',
  components: { ActionsButtons, TitleComponent },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      data: null
    }
  },

  computed: {
    id () {
      return this.data?.['id'] || this.currentRoute['params'] && this.currentRoute?.['params']['id'] || null
    }
  },

  created () {
    this.$emit('action', 'setTab', {
      key: this._.vnode.key,
      loading: true
    })

    this.get()
  },

  methods: {
    action () {
      if (typeof this[arguments[0]] === 'function') {
        this[arguments[0]](...Array.from(arguments).splice(1))
      } else {
        this.$emit('action', ...arguments)
      }
    },

    get () {
      axios.get('api/eventlog/' + this.id, {
        params: this.currentRoute['query'] || null
      }).then(r => {
        this.data = r.data.data
        this.meta = r.data.meta

        this.$emit('action', 'setTab', {
          key: this._.vnode.key,
          loading: false
        })
      })
    },

    cancel () {
      this.$emit('action', 'toTab', { name: 'EventLogs' })
    },

    delete () {
      if (confirm(this.$root.lang('confirm_delete_eventlog'))) {

      }
    }
  }
}
</script>

<style scoped>
.table > tr td {
  @apply first:pl-8 last:pr-8
}
</style>

<style>
.data-event-log {
  @apply px-6
}
.data-event-log > *:not(table) {
  @apply block mb-4
}
.data-event-log > table {
  @apply border
}
.data-event-log h2 {
  @apply font-bold text-xl
}
.data-event-log {
  @apply py-4
}
.data-event-log [style="color:red"] {
  @apply !text-rose-600
}
.data-event-log table thead th {
  @apply first:w-48 first:min-w-[12rem]
}
</style>
