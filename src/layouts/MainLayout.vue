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

        <q-btn flat @click="logout">Logout</q-btn>
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
import { Vue, Component } from 'vue-property-decorator'
import ChatWindow from 'src/components/ChatWindow.vue'
import EssentialLink from 'components/EssentialLink.vue'

@Component({
  beforeCreate () {
    if (!localStorage.getItem('token')) {
      this.$router.replace('/login')
    }
  },
  components: { EssentialLink, ChatWindow }
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false;

  get menus() {
    const userInfo = this.$store.state.AuthUser.userInfo
    const school_id: string = this.$route.params.school_id || userInfo.school_id
    const is_principal = userInfo.roles === 'as_principal'
    if (!school_id && is_principal) {
      return [
        { title: 'School', link: '/school' }
      ]
    } else if (is_principal) {
      return [
        { title: 'School', link: '/school' },
        { title: 'Teacher', link: `/school/${school_id}/teacher` },
        { title: 'Student', link: `/school/${school_id}/student` }
      ]
    } else {
      return [
        { title: 'Teacher', link: `/school/${school_id}/teacher` },
        { title: 'Student', link: `/school/${school_id}/student` }
      ]
    }
  }

  constructor () {
    super()
    this.$store.dispatch('refreshUserInfo')
  }

  logout() {
    window.location.href = '/login'
  }
}
</script>
