<script>
import { compile, h } from 'vue'

export default {
  name: 'RenderString',

  props: {
    string: {
      required: true,
      type: String
    }
  },

  render (props) {
    let string = props.string
    string = string.replace(/href=/g, 'to=').replace(/<a(.*?)>/g, '<router-link$1>').replace(/(<\/a>)/g, '</router-link>')

    return h(compile(string))
  }
}
</script>
