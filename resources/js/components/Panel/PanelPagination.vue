<template>
  <div class="pagination" v-if="!(!data?.['prev'] && !data?.['next'])">
    <div class="pages">
      <a :class="{ 'pointer-events-none opacity-50' : !data['prev'] }"
         class="btn-sm btn-gray transition"
         @mousedown="pagination(data['prev'])">
        <i class="fa fa-angle-left fa-fw"/>
      </a>
      <a :class="{ 'pointer-events-none opacity-50' : !data['next'] }"
         class="btn-sm btn-gray ml-1 transition"
         @mousedown="pagination(data['next'])">
        <i class="fa fa-angle-right fa-fw"/>
      </a>
    </div>
    <div class="info" v-if="data['info']">
      {{ data['info'] }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'PanelPagination',

  props: {
    data: {
      type: Object
    }
  },

  methods: {
    pagination (url) {
      this.$emit('action', 'pagination', url.split('?')[1] ?? '')
    }
  }
}
</script>

<style scoped>
.pagination {
  @apply pt-3 px-4 grow-0 flex flex-row flex-wrap items-center
}
.pagination .pages {
  @apply basis-auto grow basis-0
}
.pagination .info {
  @apply basis-auto grow-0 basis-0 whitespace-nowrap
}
</style>
