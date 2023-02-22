import { createStore } from 'vuex'

const state = {}

const mutations = {
  set (state, data) {
    Object.assign(state, data)
  }
}

const actions = {
  set ({ commit }, data) {
    commit('set', data)
  }
}

const getters = {
  get: (state) => (key) => {
    return key && state[key] || null
  }
}

const modules = {}

Object.entries(import.meta.glob('./modules/*.js', { eager: true })).forEach(([path, definition]) => {
  const name = path.split('/').pop().replace(/\.\w+$/, '')
  modules[name] = definition.default
})

export default createStore({
  state,
  getters,
  mutations,
  actions,
  modules
})
