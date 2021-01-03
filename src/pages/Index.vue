<template>
  <q-page class="row items-center justify-evenly">
  </q-page>
</template>

<script lang="ts">
import ExampleComponent from 'components/ClassComponent.vue'
import { Vue, Component } from 'vue-property-decorator'
import { Observer } from 'mobx-vue'

@Observer
@Component({
  components: { ExampleComponent }
})
export default class PageIndex extends Vue {
}
</script>
