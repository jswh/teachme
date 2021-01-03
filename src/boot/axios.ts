import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { boot } from 'quasar/wrappers'

declare module 'vue/types/vue' {
  interface Vue {
    $axios: AxiosInstance;
  }
}

export default boot(({ Vue }) => {
  // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access

  Vue.prototype.$axios = axios
  Vue.prototype.$axios.interceptors.request.use(function (config: AxiosRequestConfig) {
    let accessToken = localStorage.getItem('token')
    if (accessToken) {
      config.headers['Authorization'] = 'Bearer ' + accessToken
    }
    config.baseURL = 'http://127.0.0.1:8000'
    config.headers['Content-type'] = 'application/json'
    config.validateStatus = () => true
    return config
  })

  Vue.prototype.$axios.interceptors.response.use(function (res: AxiosResponse<any>) {
    if (res.data.code != 0 && !res.data['access_token']) {
      throw res.data.msg || res.data.message
    }
    return res.data
  })
})
