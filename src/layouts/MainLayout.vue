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
          Teache Me
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-1"
    >
      <q-list>
        <q-item-label
          header
          class="text-grey-8"
        >
          Essential Links
        </q-item-label>
        <EssentialLink
          v-for="link in menus"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
    <chat-window></chat-window>
  </q-layout>
</template>

<script lang="ts">
import EssentialLink from 'components/EssentialLink.vue'

import { Vue, Component } from 'vue-property-decorator'
import ChatWindow from 'components/ChatWindow'

@Component({
  beforeCreate () {
    if (!localStorage.getItem('token')) {
      this.$router.replace('/login')
    }
  },
  components: { EssentialLink , ChatWindow}
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false;
  get menus() {
    return [
      { title: 'Teacher' },
      { title: 'Student' }
    ]
  }

  constructor () {
    super()
    this.$store.dispatch('refreshUserInfo')
  }
}
</script>
