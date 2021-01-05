import { Module } from 'vuex'
import { StateInterface } from '../index'
import state, { AuthUserStateInterface } from './state'
import actions from './actions'
import getters from './getters'
import mutations from './mutations'

const AuthUser: Module<AuthUserStateInterface, StateInterface> = {
  namespaced: false,
  actions,
  getters,
  mutations,
  state
}

export default AuthUser
