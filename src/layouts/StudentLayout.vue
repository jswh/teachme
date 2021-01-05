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
          Quasar App
        </q-toolbar-title>

        <div>Quasar v{{ $q.version }}</div>
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
}
</script>
