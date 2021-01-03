<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <q-card class="my-card">
      <q-card-section>
        <div class="text-h6">Login</div>
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
          <q-btn label="Submit" type="submit" color="primary"/>
          <q-btn label="Register" to="/register" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
      </q-card-section>
    </q-card>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import {isValidEmail} from '../utils'
import store from "../mobx";

import { Observer } from "mobx-vue";
import { Token } from 'src/components/models';

@Component
export default class LoginRegister extends Vue {
  username = ''
  password = ''
  scope: any = ''
  recaptcha = ''
  isValidEmail = isValidEmail

  constructor() {
    super()
    if (this.$route.query['scope']) {
      this.scope = this.$route.query.scope
    }
  }
  async onSubmit() {
    let params = {
        "grant_type": "password",
        "client_id": "3",
        "client_secret": "g7X6P7tvc8uUE0jQ54nLmSoQpCg842dxz31dzidM",
        "scope": this.scope,
        "username": this.username,
        "password": this.password,
      }
    let err = (e: string) => {
    this.$q.notify({
      message: e,
      color: 'red',
      position: "top-right"
    })
    }
    try {
      localStorage.removeItem('token')
      let res = await this.$axios.post("/oauth/token", params) as any
      let r = this.$router;
      store.user.setAuthInfo(res)
      store.user.setScope(this.scope)
      let refreshUserInfo = store.user.refresUserInfo()
      let scope = this.scope

      let axios = this.$axios

      this.$q.notify({
        message: "login succuss",
        position: "top-right",
        timeout: 1000,
        color: 'green',
        onDismiss() {
          refreshUserInfo.then(() => {
            if (store.user.userInfo?.roles == 'as_principal') {
              params['scope'] = 'as_principal'
              axios.post("/oauth/token", params)
              .then((res: any) => {
                store.user.setAuthInfo(res)
                r.push('/school')
              })
            } else if (scope == 'as_student') {
              r.push('/home')
            } else {
              r.push(`/school/${store.user.userInfo?.school_id}/teacher`)
            }
          }).catch(e => {
            err(e)
          })
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