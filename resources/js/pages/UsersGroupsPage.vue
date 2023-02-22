<template>
  <div class="flex flex-col">

    <title-component :help="$root.lang('access_permissions_introtext')"/>

    <tabs id="usersGroups"
          :load-once="true"
          :data="[
              {
                id: 'groupUsers',
                name: $root.lang('web_access_permissions_user_groups'),
                class: 'p-4',
                active: true
              },
              {
                id: 'groupDocuments',
                name: $root.lang('access_permissions_resource_groups'),
                class: 'p-4',
              },
              {
                id: 'groupRelations',
                name: $root.lang('access_permissions_links'),
                class: 'p-4',
              }
          ]"
          ref="tabs">

      <template #groupUsers>
        <div v-html="$root.lang('access_permissions_users_tab')" class="alert-warning mb-4 p-4 rounded"/>

        <div class="font-bold">{{ $root.lang('access_permissions_add_user_group') }}</div>

        <div class="flex pt-1 pb-8">
          <button class="btn-sm btn-green">
            {{ $root.lang('submit') }}
          </button>
          <input type="text" class="px-3 py-1 ml-1 rounded w-full">
        </div>

        <div class="-mx-4">
          <panel :data="this.dataUsers" :current-route="currentRoute"/>
        </div>
      </template>

      <template #groupDocuments>
        <div v-html="$root.lang('access_permissions_resources_tab')" class="alert-warning mb-4 p-4 rounded"/>

        <div class="font-bold">{{ $root.lang('access_permissions_add_resource_group') }}</div>

        <div class="flex pt-1 pb-8">
          <button class="btn-green btn-sm">
            {{ $root.lang('submit') }}
          </button>
          <input type="text" class="px-3 py-1 ml-1 rounded w-full">
        </div>

        <div class="-mx-4">
          <panel :data="this.dataDocuments" :current-route="currentRoute"/>
        </div>
      </template>

      <template #groupRelations>
        <div v-html="$root.lang('access_permissions_links_tab')" class="alert-warning mb-4 p-4 rounded"/>

        <div class="font-bold mb-2">{{ $root.lang('access_permissions_group_link') }}</div>

        <div class="md:flex flex-row flex-wrap pt pb-8 items-center">
          <span class="flex sm:items-center flex-col sm:flex-row md:inline-block pb-2">
            <span class="pb-1 sm:pb-0">{{ $root.lang('access_permissions_link_user_group') }}</span>
            <select name="group-users" class="sm:mx-3 pl-3 pr-8 py-1 rounded w-full md:w-auto grow">
              <option v-for="i in dataRelationsUsers" :value="i.key">{{ i.value }}</option>
            </select>
          </span>

          <span class="flex sm:items-center flex-col sm:flex-row md:inline-block pb-2">
            <span class="pb-1 sm:pb-0">{{ $root.lang('access_permissions_link_to_group') }}</span>
            <select name="group-resources" class="sm:mx-3 pl-3 pr-8 py-1 rounded w-full md:w-auto grow">
              <option v-for="i in dataRelationsDocuments" :value="i.key">{{ i.value }}</option>
            </select>
          </span>

          <span class="flex items-center justify-between md:inline-block pb-2">
            <span>
              <span>{{ $root.lang('access_permissions_context') }}</span>

              <label class="ml-3">
                <input type="checkbox" value="mgr" class="mr-2 rounded">
                <span>mgr</span>
              </label>

              <label class="ml-3">
                <input type="checkbox" value="web" class="mr-2 rounded">
                <span>web</span>
              </label>
            </span>

            <button class="btn-sm btn-green ml-3">{{ $root.lang('submit') }}</button>
          </span>
        </div>

        <div class="-mx-4">
          <panel :data="this.dataRelations" :current-route="currentRoute"/>
        </div>
      </template>

    </tabs>

  </div>
</template>

<script>
import TitleComponent from '@/components/Layout/Title.vue'
import Tabs from '@/components/Tabs/Tabs.vue'
import Panel from '@/components/Panel/Panel.vue'

export default {
  name: 'UsersPermissionsPage',
  components: { TitleComponent, Tabs, Panel },

  props: {
    currentRoute: Object
  },

  data () {
    return {
      dataUsers: null,
      dataDocuments: null,
      dataRelations: null,
      dataRelationsUsers: [],
      dataRelationsDocuments: []
    }
  },

  mounted () {
    this.get()

    this.$watch(
        () => this.$refs.tabs.active,
        tab => {
          switch (tab) {
            case 'groupUsers':
              !this.dataUsers && this.groupUsers()
              break

            case 'groupDocuments':
              !this.dataDocuments && this.groupDocuments()
              break

            case 'groupRelations':
              !this.dataRelations && this.groupRelations()
              break
          }
        }
    )
  },

  methods: {
    get () {
      if (this[this.$refs.tabs?.active]) {
        this[this.$refs.tabs.active]()
      }
    },

    groupUsers () {
      axios.get('api/usersmanagement/users', {}).then(r => {
        this.dataUsers = {
          data: r.data.data,
          columns: [
            {
              name: 'name',
              label: this.$root.lang('name'),
              width: '20rem',
              component: 'Fields/Input'
            },
            {
              name: 'users',
              label: this.$root.lang('access_permissions_users_in_group')
            },
            {
              name: 'actions',
              label: this.$root.lang('mgrlog_action'),
              width: '3rem',
              style: {
                textAlign: 'center'
              }
            }
          ]
        }

        for (const item of this.dataUsers.data) {
          if (item.users.length) {
            item['users.html'] = item.users.map(i => {
              return '<a href="user/' + i.id + '" class="mr-1 link">' + i.username + '</a> '
            }).join('')
          } else {
            item['users.html'] = '<span class="opacity-50">' + this.$root.lang('access_permissions_no_users_in_group') +
                '</span>'
          }

          item['actions.html'] = '<i class="fa fa-trash hover:text-rose-600 cursor-pointer">'
        }
      })
    },

    groupDocuments () {
      axios.get('api/usersmanagement/documents', {}).then(r => {
        this.dataDocuments = {
          data: r.data.data,
          columns: [
            {
              name: 'name',
              label: this.$root.lang('name'),
              width: '20rem',
              component: 'Fields/Input'
            },
            {
              name: 'documents',
              label: this.$root.lang('access_permissions_resources_in_group')
            },
            {
              name: 'actions',
              label: this.$root.lang('mgrlog_action'),
              width: '3rem',
              style: {
                textAlign: 'center'
              }
            }
          ]
        }

        for (const item of this.dataDocuments.data) {
          if (item['documents'].length) {
            item['documents.html'] = item['documents'].map(i => {
              return '<a href="document/' + i.id + '" class="mr-1 link">' + i['pagetitle'] + ' (' + i['id'] + ')</a> '
            }).join('')
          } else {
            item['documents.html'] = '<span class="opacity-50">' +
                this.$root.lang('access_permissions_no_resources_in_group') + '</span>'
          }

          item['actions.html'] = '<i class="fa fa-trash hover:text-rose-600 cursor-pointer">'
        }
      })
    },

    groupRelations () {
      axios.get('api/usersmanagement/relations', {}).then(r => {
        for (const i of r.data.data['groupDocuments']) {
          this.dataRelationsDocuments.push({
            key: i.id,
            value: i.name
          })
        }

        this.dataRelations = {
          data: r.data.data['groupUsers'],
          columns: [
            {
              name: 'name',
              label: this.$root.lang('name'),
              style: {
                fontWeight: 500
              }
            },
            {
              name: 'document_groups',
              label: this.$root.lang('access_permissions_resource_groups')
            }
          ]
        }

        for (const item of this.dataRelations.data) {
          this.dataRelationsUsers.push({
            key: item.id,
            value: item.name
          })

          if (item['document_groups'].length) {
            item['document_groups.html'] = item['document_groups'].map(i => {
              return '' +
                  '<div class="pb-1">' +
                  ' <span class="font-medium">' + i.name + '</span> (' + (i['pivot'].context ? 'web' : 'mgr') + ') ' +
                  ' <a class="link text-rose-500 hover:text-rose-600">' + this.$root.lang('delete') + '</a>' +
                  '</div>'
            }).join('')
          } else {
            item['document_groups.html'] = '<span class="opacity-50">' + this.$root.lang('no_groups_found') + '</span>'
          }
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
