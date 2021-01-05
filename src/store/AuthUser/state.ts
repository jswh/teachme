import { Student, Teacher } from 'src/components/models'

export interface AuthUserStateInterface {
  access_token?: string
  refresh_token?: string
  expires_in?: number
  scope?: string
  userInfo?: Teacher | Student
}

function state(): AuthUserStateInterface {
  let userInfo: any = localStorage.getItem('user_info') || undefined
  if (userInfo) {
    userInfo = JSON.parse(userInfo)
  }
  return {
    access_token: localStorage.getItem('access_token') || undefined,
    scope: localStorage.getItem('scope') || undefined,
    userInfo: userInfo
  }
}

export default state
