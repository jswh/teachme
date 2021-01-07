<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <q-card class="my-card">
      <q-card-section>
        <div class="text-h6">Login <span v-if="idToken">And Bind Line</span></div>
      </q-card-section>
      <q-separator />
      <q-card-section>

      <q-form @submit="onSubmit" class="q-gutter-md">
        <q-input color="grey-3" label-color="blue" outlined v-model="username" label="Username/Email"
        :rules="[ val => val && val.length > 0 || 'Please input your username or email']">
          <template v-slot:append>
            <q-icon name="person" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" outlined v-model="password" label="Password" type="password"
        :rules="[ val => val && val.length > 0 || 'Please input your password']">
          <template v-slot:append>
            <q-icon name="password" />
          </template>
        </q-input>
        <div class="q-gutter-sm">
        <q-radio v-model="scope" val="as_student" label="I am student" />
        <q-radio v-model="scope" val="as_teacher" label="I am teacher" />
    </div>
        <div>
          <q-btn :loading="dologin" label="Submit" type="submit" color="primary"/>
          <q-btn label="Register" to="/register" color="primary" flat class="q-ml-sm" />
        </div>
        <div v-if="!idToken">
          <a :href="line_access_url" style="text-decoration: none;">
            <q-btn label="Login with Line" color="secondary"/>
          </a>
        </div>
      </q-form>
      </q-card-section>
    </q-card>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { AppConfig, isValidEmail, serializeQuery } from '../utils'

@Component
export default class LoginRegister extends Vue {
  username = ''
  password = ''
  scope = ''
  recaptcha = ''
  isValidEmail = isValidEmail
  dologin = false

  get line_access_url() {
    const q = serializeQuery(AppConfig.line_login) + '&redirect_uri=' + window.location.protocol + '//' + window.location.host + '/withline'
    return 'https://access.line.me/oauth2/v2.1/authorize?' + q
  }

  get idToken() {
    return sessionStorage.getItem('line_id_token')
  }

  constructor () {
    super()
    if (this.$route.query.scope) {
      this.scope = this.$route.query.scope as string
    }
  }

  async onSubmit () {
    this.dologin = true
    const params: any = {
      grant_type: 'password',
      client_id: AppConfig.oauth_client_id,
      client_secret: AppConfig.oauth_client_key,
      scope: this.scope,
      username: this.username,
      password: this.password
    }

    const err = (e: string) => {
      this.dologin = false
      this.$q.notify({
        message: e,
        color: 'red',
        position: 'top-right'
      })
    }
    try {
      localStorage.removeItem('token')
      const res = await this.$axios.post('/oauth/token', params) as any
      const r = this.$router
      const store = this.$store
      store.commit('setAuthInfo', res)
      store.commit('setScope', res)
      store.dispatch('refreshUserInfo')
      const scope = this.scope

      const axios = this.$axios
      const id_token = sessionStorage.getItem('line_id_token')
      if (id_token) {
        await this.$axios.put('/api/line/binding', {
          id_token
        })
        sessionStorage.removeItem('line_id_token')
      }

      this.$q.notify({
        message: 'login succuss',
        position: 'top-right',
        timeout: 1000,
        color: 'green',
        onDismiss () {
          if (store.state.AuthUser.userInfo.roles === 'as_principal') {
            params.scope = 'as_principal'
            axios.post('/oauth/token', params)
              .then((res: any) => {
                store.commit('setAuthInfo', res)
                r.push('/school')
              })
          } else if (scope === 'as_student') {
            r.push('/')
          } else {
            r.push(`/school/${store.state.AuthUser.userInfo?.school_id as string}/teacher`)
          }
        }
      })
    } catch (e) {
      err(e)
    }
  }
}
</script>

<style lang="stylus" scoped>
  .my-card
    width 50%
    max-width 500px
    padding 2em
    margin auto
    margin-top 100px

</style>
