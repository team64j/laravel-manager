const storageKey = 'CMS'
const state = JSON.parse(localStorage[storageKey] || '{}')

const mutations = {
  set (state, data) {
    let key, value

    if (Array.isArray(data)) {
      [key, value] = data
      if (typeof state[key] === 'object') {
        Object.assign(state[key], value)
      } else {
        state[key] = value
      }
    } else if (typeof data === 'object') {
      Object.assign(state, data)
    }

    localStorage[storageKey] = JSON.stringify(state)
  },

  del (state, key) {
    if (state[key] !== undefined) {
      delete state[key]
    }
    localStorage[storageKey] = JSON.stringify(state)
  }
}

const actions = {
  set ({ commit }, data) {
    commit('set', data)
  },

  del ({ commit }, key) {
    commit('del', key)
  }
}

const getters = {
  get: (state) => (key, def) => {
    return (key && state[key]) ?? def ?? null
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
