import { action, observable } from 'mobx'
import { Student, Token } from 'components/models'
import { AxiosInstance } from 'axios'
import { Teacher } from '../components/models'
export default class AuthUser {
  http?: AxiosInstance
  constructor(httpClient?: AxiosInstance) {
    this.http = httpClient
    let authInfo = null
    try {
      authInfo = localStorage.getItem('auth_info')
      if (authInfo) {
        authInfo = JSON.parse(authInfo)
      }
    } catch (e) { }

    if (authInfo) {
      this.setAuthInfo(authInfo)
    }
    const scope = localStorage.getItem('scope')
    if (scope) {
      this.scope = scope
    }
    const userInfo = localStorage.getItem('user_info')
    if (userInfo) {
      this.userInfo = JSON.parse(userInfo)
    }
  }

  setHttpClient(httpClient?: AxiosInstance) {
    this.http = httpClient
  }

  @observable access_token = '';
  @observable refresh_token = '';
  @observable expires_in = 0;
  @observable scope = ''
  @observable userInfo?: Teacher | Student

  @action.bound setAuthInfo(authInfo: Token) {
    localStorage.setItem('auth_info', JSON.stringify(authInfo))
    this.access_token = authInfo.access_token
    this.refresh_token = authInfo.refresh_token
    this.expires_in = authInfo.expires_in
    localStorage.setItem('token', this.access_token)
  }

  @action.bound setScope(scope: string) {
    this.scope = scope
    localStorage.setItem('scope', scope)
  }

  @action.bound async refresUserInfo() {
    const res = await this.http?.get('/api/me') as any
    this.userInfo = res.data
    localStorage.setItem('user_info', JSON.stringify(this.userInfo))
  }
}
