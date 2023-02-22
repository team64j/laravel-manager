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

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
