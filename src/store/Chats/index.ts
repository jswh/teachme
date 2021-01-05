import { Module } from 'vuex'
import { StateInterface } from '../index'
import state, { ChatsStateInterface } from './state'
import actions from './actions'
import getters from './getters'
import mutations from './mutations'

const exampleModule: Module<ChatsStateInterface, StateInterface> = {
  namespaced: false,
  actions,
  getters,
  mutations,
  state
}

export default exampleModule
