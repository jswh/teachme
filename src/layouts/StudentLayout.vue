<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <q-toolbar-title>
          Teacherme
        </q-toolbar-title>

        <q-btn flat @click="logout">Logout</q-btn>
      </q-toolbar>
    </q-header>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component({
  beforeCreate () {
    if (!localStorage.getItem('token')) {
      this.$router.replace('/login')
    }
  }
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false;

  constructor () {
    super()
    this.$store.dispatch('refreshUserInfo')
  }

  logout() {
    window.location.href = '/login'
  }
}
</script>
