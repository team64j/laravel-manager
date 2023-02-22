<template>
  <div class="template" :class="$props.class">
    <slot name="default"/>
  </div>
</template>

<script>
export default {
  name: 'LayoutTemplate',

  props: {
    class: [Array, Object, String]
  }
}
</script>
