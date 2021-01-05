<template>
  <div class="fullscreen bg-blue text-white text-center q-pa-md flex flex-center">
    login...
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component
export default class PageLineCallback extends Vue {
  constructor() {
    super()
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code')
    if (code) {
      this.init(code)
    }
  }

  async init(code: string) {
    const res = await this.$axios.get('/api/line/token/' + code)

  }

}
</script>
