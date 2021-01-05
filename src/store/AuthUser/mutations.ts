import { MutationTree } from 'vuex'
import { AuthUserStateInterface } from './state'
import { Student, Teacher, Token } from 'components/models'

const mutation: MutationTree<AuthUserStateInterface> = {
  setAuthInfo(state, authInfo: Token) {
    localStorage.setItem('auth_info', JSON.stringify(authInfo))
    state.access_token = authInfo.access_token
    state.refresh_token = authInfo.refresh_token
    state.expires_in = authInfo.expires_in
    localStorage.setItem('token', state.access_token)
  },
  setScope(state, scope: string) {
    state.scope = scope
    localStorage.setItem('scope', scope)
  },
  setUserInfo(state, userInfo: Teacher | Student) {
    state.userInfo = userInfo
    localStorage.setItem('user_info', JSON.stringify(userInfo))
  }
}

export default mutation
