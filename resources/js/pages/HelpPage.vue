<template>
  <div class="flex flex-col">

    <title-component/>

    <div class="p-6 bg-white dark:bg-cms-700" v-if="data">
      <div class="data border mb-7">
        <table>
          <thead>
          <tr>
            <th colspan="2" class="text-left">{{ $root.lang('evo_downloads_title') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>
              <strong>{{ $root.lang('info') }}</strong>
            </td>
            <td>
              <a href="https://evocms.ru/" target="_blank">https://evocms.ru/</a>
            </td>
          </tr>
          <tr>
            <td>
              <strong>Скачать</strong>
            </td>
            <td>
              <a href="https://github.com/evocms-community/evolution/releases"
                 target="_blank">https://github.com/evocms-community/evolution/releases</a>
            </td>
          </tr>
          <tr>
            <td>
              <strong>{{ $root.lang('extras') }}</strong>
            </td>
            <td>
              <a href="https://github.com/extras-evolution"
                 target="_blank">https://github.com/extras-evolution</a>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="data border mb-7">
        <table>
          <thead>
          <tr>
            <th colspan="2" class="text-left">{{ $root.lang('help_translating_title') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>
              <strong>Evolution CMS</strong>
            </td>
            <td>
              <a href="https://www.transifex.com/evolutioncms/evolution/"
                 target="_blank">https://www.transifex.com/evolutioncms/evolution/</a>
            </td>
          </tr>
          <tr>
            <td>
              <strong>{{ $root.lang('extras') }}</strong>
            </td>
            <td>
              <a href="https://www.transifex.com/evolutioncms/extras/"
                 target="_blank">https://www.transifex.com/evolutioncms/extras/</a>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="data border mb-7">
        <table>
          <thead>
          <tr>
            <th class="text-left">
              {{ $root.lang('about_title') }}
            </th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>
              <div v-html="$root.lang('about_msg')"/>
              <div v-html="$root.lang('credits_shouts_msg')"/>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="data border">
        <table>
          <thead>
          <tr>
            <th colspan="3" class="text-left">{{ $root.lang('documentation') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>
              <a href="http://www.evolution-docs.com/"
                 target="_blank">What is EVO?</a>
            </td>
            <td></td>
            <td>Official EVO documentation</td>
          </tr>
          <tr>
            <td>
              <a href="https://evo-cms.com/"
                 target="_blank">evo-cms.com</a>
            </td>
            <td></td>
            <td>Home of EVO</td>
          </tr>
          <tr>
            <td>
              <a href="http://forum.evolution-cms.com/"
                 target="_blank">EVO Forums</a>
            </td>
            <td></td>
            <td>General support & discussions</td>
          </tr>
          <tr>
            <td>
              <a href="http://docs.evolution-cms.com/"
                 target="_blank">docs.evolution-cms.com</a>
            </td>
            <td>
              <a href="https://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http://docs.evolution-cms.com/"
                 target="_blank">Google Translate</a>
            </td>
            <td>Further documentation</td>
          </tr>
          <tr>
            <td>
              <a href="http://saniock.com/modx-evolution/developer/api.html"
                 target="_blank">API Reference</a></td>
            <td>
              <a href="https://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http://saniock.com/modx-evolution/developer/api.html"
                 target="_blank">Google Translate</a></td>
            <td>Further documentation</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="flex items-center justify-center grow">
      <loader/>
    </div>

  </div>
</template>

<script>
import TitleComponent from '@/components/Layout/Title.vue'

export default {
  name: 'HelpPage',
  components: { TitleComponent },

  data () {
    return {
      data: null
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
    get () {
      this.data = []

      this.$emit('action', 'setTab', {
        key: this._.vnode.key,
        loading: false
      })
    }
  }
}
</script>

<style scoped>

</style>
