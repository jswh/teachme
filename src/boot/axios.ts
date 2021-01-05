import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { boot } from 'quasar/wrappers'
import { AppConfig } from 'src/utils'

declare module 'vue/types/vue' {
  interface Vue {
    $axios: AxiosInstance;
  }
}

export const http = axios.create()

http.interceptors.request.use(function (config: AxiosRequestConfig) {
  const accessToken = localStorage.getItem('token')
  if (accessToken) {
    config.headers.Authorization = 'Bearer ' + accessToken
  }
  config.baseURL = AppConfig.api_base

  config.headers['Content-type'] = 'application/json'
  config.validateStatus = () => true
  return config
})

http.interceptors.response.use(function (res: AxiosResponse<any>) {
  if (res.data.code !== 0 && !res.data.access_token) {
    throw res.data.msg || res.data.message
  }
  // eslint-disable-next-line @typescript-eslint/no-unsafe-return
  return res.data
})

export default boot(({ Vue }) => {
  Vue.prototype.$axios = http
})
