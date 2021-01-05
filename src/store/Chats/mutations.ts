import { MutationTree } from 'vuex'
import { ChatsStateInterface } from './state'

const mutation: MutationTree<ChatsStateInterface> = {
  refreshChats(state, chats) {
    console.log(chats)
    state.chats = { ...chats }
  }
}

export default mutation
