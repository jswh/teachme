<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <q-card class="my-card">
      <q-card-section>
        <div class="text-h6">Register A Principal</div>
      </q-card-section>
      <q-separator />
      <q-card-section>

      <q-form @submit="onSubmit" class="q-gutter-md">
        <q-input color="grey-3" label-color="blue" outlined v-model="name" label="Name"
        :rules="[ val => val && val.length > 0 || 'Please tell us your name']">
          <template v-slot:append>
            <q-icon name="person" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" type="email" outlined v-model="email" label="Email"
        :rules="[ val => isValidEmail(val) || 'Please use a correct eamil address']">
          <template v-slot:append>
            <q-icon name="email" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" outlined v-model="password" label="Password" type="password"
        :rules="[ val => val && val.length > 0 || 'Please choose a password']">
          <template v-slot:append>
            <q-icon name="password" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" type="password" outlined v-model="password_confirmation" label="Confirm Password"
        :rules="[ val => val == password || 'Your password confirmation not match']">
        </q-input>
        <div>
          <q-btn label="Submit" type="submit" color="primary"/>
          <q-btn label="Login" to="/login" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
      </q-card-section>
    </q-card>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { isValidEmail } from '../utils'

@Component({
  components: { }
})
export default class PageRegister extends Vue {
  name = ''
  email = ''
  password = ''
  password_confirmation = ''
  recaptcha = ''

  isValidEmail = isValidEmail
  async onSubmit () {
    try {
      await this.$axios.post('/api/register/principal', {
        email: this.email,
        name: this.name,
        password: this.password,
        password_confirmation: this.password_confirmation
      })
      const r = this.$router
      this.$q.notify({
        message: 'register succuss',
        position: 'top-right',
        timeout: 1000,
        color: 'green',
        onDismiss () {
          r.push('/login')
        }
      })
    } catch (e) {
      this.$q.notify({
        message: e,
        color: 'red',
        position: 'top-right'
      })
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
