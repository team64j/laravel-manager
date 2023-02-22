import { createRouter, createWebHistory } from 'vue-router'
import { notify } from '@kyvg/vue3-notification'
import store from '@/store'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('@/pages/DashboardPage'),
    meta: {
      fixed: true,
      title: '',
      icon: 'fa fa-home',
      class: 'tab-home'
    }
  },
  {
    path: '/document/:id?',
    name: 'Document',
    component: () => import('@/pages/DocumentPage'),
    meta: {
      icon: 'fa fa-edit',
      lang: 'new_resource'
    }
  },
  {
    path: '/documents/:id',
    name: 'Documents',
    component: () => import('@/pages/DocumentsPage'),
    meta: {
      icon: 'fa fa-edit'
    }
  },
  {
    path: '/elements/:element',
    name: 'Elements',
    component: () => import('@/pages/ElementsPage'),
    meta: {
      icon: 'fa fa-th',
      lang: 'elements',
      group: true
    }
  },
  {
    path: '/template/:id?',
    name: 'Template',
    component: () => import('@/pages/TemplatePage'),
    meta: {
      icon: 'fa fa-newspaper',
      lang: 'new_template',
      permissions: ['edit_template']
    }
  },
  {
    path: '/tv/:id?',
    name: 'Tv',
    component: () => import('@/pages/TvPage'),
    meta: {
      icon: 'fa fa-list-alt',
      lang: 'new_tmplvars'
    }
  },
  {
    path: '/tvs/sort',
    name: 'TvSort',
    component: () => import('@/pages/TvSortPage'),
    meta: {
      icon: 'fa fa-sort-numeric-asc',
      lang: 'template_tv_edit_title'
    }
  },
  {
    path: '/chunk/:id?',
    name: 'Chunk',
    component: () => import('@/pages/ChunkPage'),
    meta: {
      icon: 'fa fa-th-large',
      lang: 'new_htmlsnippet'
    }
  },
  {
    path: '/snippet/:id?',
    name: 'Snippet',
    component: () => import('@/pages/SnippetPage'),
    meta: {
      icon: 'fa fa-code',
      lang: 'new_snippet'
    }
  },
  {
    path: '/plugin/:id?',
    name: 'Plugin',
    component: () => import('@/pages/PluginPage'),
    meta: {
      icon: 'fa fa-plug',
      lang: 'new_plugin'
    }
  },
  {
    path: '/plugins/sort',
    name: 'PluginSort',
    component: () => import('@/pages/PluginSortPage'),
    meta: {
      icon: 'fa fa-sort-numeric-asc',
      lang: 'plugin_priority_title'
    }
  },
  {
    path: '/module/:id?',
    name: 'Module',
    component: () => import('@/pages/ModulePage'),
    meta: {
      icon: 'fa fa-cube',
      lang: 'new_module'
    }
  },
  {
    path: '/module/exec/:id?',
    name: 'ModuleExec',
    component: () => import('@/pages/ModuleExecPage'),
    meta: {
      icon: 'fa fa-cube',
      lang: 'run_module',
      isIframe: true
    }
  },
  {
    path: '/category/:id?',
    name: 'Category',
    component: () => import('@/pages/CategoryPage'),
    meta: {
      icon: 'fa fa-object-group',
      lang: 'new_category'
    }
  },
  {
    path: '/categories/sort',
    name: 'CategorySort',
    component: () => import('@/pages/CategorySortPage'),
    meta: {
      icon: 'fa fa-sort-numeric-asc',
      lang: 'cm_sort_categories'
    }
  },
  {
    path: '/user/:id?',
    name: 'User',
    component: () => import('@/pages/UserPage'),
    meta: {
      icon: 'fa fa-user-circle',
      lang: 'new_user'
    }
  },
  {
    path: '/users',
    name: 'Users',
    component: () => import('@/pages/UsersPage'),
    meta: {
      icon: 'fa fa-users',
      lang: 'users'
    }
  },
  {
    path: '/users/:element',
    name: 'UsersManagement',
    component: () => import('@/pages/UsersManagementPage'),
    meta: {
      icon: 'fa fa-legal',
      lang: 'role_role_management',
      group: true
    }
  },
  {
    path: '/users/role/:id?',
    name: 'UsersRole',
    component: () => import('@/pages/UsersRolePage'),
    meta: {
      icon: 'fa fa-legal',
      lang: 'new_role'
    }
  },
  {
    path: '/users/category/:id?',
    name: 'UsersCategory',
    component: () => import('@/pages/UsersCategoryPage'),
    meta: {
      icon: 'fa fa-object-group',
      lang: 'groups_permission_title'
    }
  },
  {
    path: '/users/permission/:id?',
    name: 'UsersPermission',
    component: () => import('@/pages/UsersRolePage'),
    meta: {
      icon: 'fa fa-user-tag',
      lang: 'permission_title'
    }
  },
  {
    path: '/users/groups',
    name: 'UsersGroups',
    component: () => import('@/pages/UsersGroupsPage'),
    meta: {
      icon: 'fa fa-male',
      lang: 'manage_permission'
    }
  },
  {
    path: '/cache',
    name: 'Cache',
    component: () => import('@/pages/CachePage'),
    meta: {
      icon: 'fa fa-recycle',
      lang: 'refresh_site'
    }
  },
  {
    path: '/search',
    name: 'Search',
    component: () => import('@/pages/SearchPage'),
    meta: {
      icon: 'fa fa-search',
      lang: 'search'
    }
  },
  {
    path: '/configuration',
    name: 'Configuration',
    component: () => import('@/pages/ConfigurationPage'),
    meta: {
      icon: 'fa fa-sliders',
      lang: 'settings_title'
    }
  },
  {
    path: '/schedules',
    name: 'Schedules',
    component: () => import('@/pages/SchedulesPage'),
    meta: {
      icon: 'far fa-calendar',
      lang: 'site_schedule'
    }
  },
  {
    path: '/event-logs',
    name: 'EventLogs',
    component: () => import('@/pages/EventLogsPage'),
    meta: {
      icon: 'fa fa-exclamation-triangle',
      lang: 'eventlog_viewer'
    }
  },
  {
    path: '/event-log/:id',
    name: 'EventLog',
    component: () => import('@/pages/EventLogPage'),
    meta: {
      icon: 'fa fa-exclamation-triangle',
      lang: 'eventlog'
    }
  },
  {
    path: '/system-log',
    name: 'SystemLog',
    component: () => import('@/pages/SystemLogPage'),
    meta: {
      icon: 'fa fa-user-secret',
      lang: 'mgrlog_view'
    }
  },
  {
    path: '/system-info',
    name: 'SystemInfo',
    component: () => import('@/pages/SystemInfoPage'),
    meta: {
      icon: 'fa fa-info',
      lang: 'view_sysinfo'
    }
  },
  {
    path: '/help',
    name: 'Help',
    component: () => import('@/pages/HelpPage'),
    meta: {
      icon: 'far fa-question-circle',
      lang: 'help'
    }
  },
  {
    path: '/password/change',
    name: 'PasswordChange',
    component: () => import('@/pages/PasswordChangePage'),
    meta: {
      icon: 'fa fa-lock',
      lang: 'change_password'
    }
  },
  {
    path: '/files/:id?',
    name: 'Files',
    component: () => import('@/pages/FilesPage'),
    meta: {
      group: true,
      icon: 'far fa-folder-open',
      lang: 'files_management'
    }
  },
  {
    path: '/file/:id?',
    name: 'File',
    component: () => import('@/pages/FilePage'),
    meta: {
      group: true,
      icon: 'far fa-file',
      lang: 'files_management'
    }
  },
  {
    path: '/logout',
    name: 'Logout'
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFoundPage',
    component: () => import('@/pages/NotFoundPage'),
    meta: {
      icon: ''
    }
  },
  {
    path: '/redirect',
    component: () => import('@/pages/RedirectPage'),
    hidden: true,
    children: [
      {
        path: '/redirect/:path(.*)',
        component: () => import('@/pages/RedirectPage')
      }
    ],
    meta: {
      hidden: true
    }
  }
]

const router = createRouter({
  history: createWebHistory(document.baseURI.replace(location.origin, '')),
  routes
})

router.beforeEach(async (to) => {
  await store.dispatch('Auth/check')

  if (!store.getters['Auth/isAuth']) {
    location.href = document.baseURI.replace(location.origin, '') + 'logout'
  }

  if (to.meta['lang']) {
    to.meta['title'] = store.getters['Lang/get'](to.meta['lang'])
  }

  await store.dispatch('set', { saving: false })

  if (to.meta['permissions'] && !store.getters['Auth/hasPermissions'](to.meta['permissions'])) {
    notify({
      text: store.getters['Lang/get']('error_no_privileges'),
      type: 'error'
    })
    return false
  }
})

export default router
