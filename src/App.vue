<template>
  <div id="q-app">
    <router-view />
  </div>
</template>
<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import chatService from './boot/chats'

@Component
export default class App extends Vue {
  constructor() {
    super()
    chatService.setStore(this.$store)
  }
}
</script>
