import store from '@/store'

const state = {}

const mutations = {
  set (state, data) {
    Object.assign(state, data)
  }
}

const actions = {
  async check ({ commit }) {
    if (!state.role) {
      let response = await axios.get('manager/api/bootstrap')

      if (response.status === 200) {
        await commit('set', response.data.data['user'])
        await store.dispatch('set', { menu: response.data.data['menu'] })
        await store.dispatch('Config/set', response.data.data['config'])
        await store.dispatch('Lang/set', response.data.data['lexicon'])
      }
    }

    return !!state.role
  },

  set ({ commit }, data) {
    commit('set', data)
  }
}

const getters = {
  hasPermissions: (state) => (permissions) => {
    if (typeof permissions === 'object') {
      return permissions.some(permission => ~state.permissions?.indexOf(permission) || false)
    } else {
      return ~state.permissions?.indexOf(permissions) || false
    }
  },

  user: (state) => {
    return state
  },

  role: (state) => {
    return state?.role || null
  },

  username (state) {
    return state?.username || null
  },

  isAuth (state) {
    return !!state
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
