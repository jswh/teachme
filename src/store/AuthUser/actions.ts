import { ActionTree } from 'vuex'
import { StateInterface } from '../index'
import { AuthUserStateInterface } from './state'
import { http } from '../../boot/axios'
import chatService from '../../boot/chats'

const actions: ActionTree<AuthUserStateInterface, StateInterface> = {
  async refreshUserInfo(context) {
    const res = await http.get('/api/me') as any
    context.commit('setUserInfo', res.data)
    chatService.connect(res.data)
  }
}

export default actions
