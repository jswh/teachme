<template>
  <q-page class="row items-center justify-evenly">
  </q-page>
</template>

<script lang="ts">
import { Todo, Meta } from 'components/models'
import ExampleComponent from 'components/ClassComponent.vue'
import { Vue, Component } from 'vue-property-decorator'
import { Observer } from 'mobx-vue'
import AuthUser from 'src/mobx/auth-user'
import store from '../mobx'

@Observer
@Component({
  components: { ExampleComponent },
})
export default class PageIndex extends Vue {
  constructor() {
    super()
  }
  async loadSchool() {
  }
}
</script>
