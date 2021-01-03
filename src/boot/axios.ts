import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { boot } from 'quasar/wrappers'

declare module 'vue/types/vue' {
  interface Vue {
    $axios: AxiosInstance;
  }
}

export default boot(({ Vue }) => {
  const instance = axios.create()
  instance.interceptors.request.use(function (config: AxiosRequestConfig) {
    const accessToken = localStorage.getItem('token')
    if (accessToken) {
      config.headers.Authorization = 'Bearer ' + accessToken
    }
    // config.baseURL = 'http://127.0.0.1:8000'
    config.baseURL = 'https://teachmenow.herokuapp.com'

    config.headers['Content-type'] = 'application/json'
    config.validateStatus = () => true
    return config
  })

  instance.interceptors.response.use(function (res: AxiosResponse<any>) {
    if (res.data.code !== 0 && !res.data.access_token) {
      throw res.data.msg || res.data.message
    }
    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return res.data
  })
  Vue.prototype.$axios = instance
})
