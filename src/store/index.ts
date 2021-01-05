import { store } from 'quasar/wrappers'
import Vuex from 'vuex'

// import example from './module-example';
// import { ExampleStateInterface } from './module-example/state';
import AuthUser from './AuthUser'
import { AuthUserStateInterface } from './AuthUser/state'
import Chats from './Chats'
import { ChatsStateInterface } from './Chats/state'
/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export interface StateInterface {
  // Define your own store structure, using submodules if needed
  // example: ExampleStateInterface;
  // Declared as unknown to avoid linting issue. Best to strongly type as per the line above.
  authUser: AuthUserStateInterface,
  chats: ChatsStateInterface
}

export default store(function ({ Vue }) {
  Vue.use(Vuex)

  const Store = new Vuex.Store<StateInterface>({
    modules: {
      AuthUser,
      Chats
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: !!process.env.DEBUGGING
  })
  return Store
})
