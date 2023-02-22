<script>
import { getCurrentInstance, h, reactive, watchEffect } from 'vue'

export default {
  name: 'PanelItemComponent',

  emits: ['action', 'update:modelValue'],

  props: [
    'modelValue',
    'props',
    'category',
    'item'
  ],

  setup (props, { emit }) {
    const component = Vue._context.components[props.props.component]
    const attrs = reactive({...(props.props.attrs || {})})

    attrs.modelValue = props.modelValue
    attrs.key = props.props.model || getCurrentInstance().vnode.key

    attrs.onAction = function () {
      emit('action', ...arguments)
    }

    attrs['onUpdate:modelValue'] = function () {
      emit('update:modelValue', ...arguments)
    }

    watchEffect(() => {
      if (props.modelValue !== undefined) {
        attrs.modelValue = props.modelValue
      }
    })

    return () => h(component, attrs)
  }
}
</script>
