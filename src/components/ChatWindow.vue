<template>
  <div class="q-pa-md row items-start q-gutter-md">
      <q-form @submit="onSubmit" class="q-gutter-md">
        <q-input color="grey-3" label-color="blue" outlined v-model="name" label="name"
        :rules="[ val => val && val.length > 0 || 'Please input your school name']">
        </q-input>
        <q-input color="grey-3" label-color="blue" outlined v-model="description" label="description"
        :rules="[ val => val && val.length > 0 || 'Please input your school description']">
        </q-input>
        <div>
          <q-btn label="Submit" type="submit" color="primary"/>
          <q-btn label="Cancel" @click="onCancel" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
    </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator'
import {isValidEmail} from '../utils'
import store from "../mobx";

import { Observer } from "mobx-vue";
import { School, Token } from 'src/components/models';
@Component({
  components: { }
})
export default class SchoolApply extends Vue {
  @Prop() readonly onCancel!: () => void;
  @Prop() readonly onSuccess!: (school: School) => void;

  name = ''
  description = ''

  async onSubmit() {
    try {
      let res = await this.$axios.post("/api/schools", {
        "name": this.name,
        "description": this.description,
      }) as any
      this.onSuccess(res.data)
    } catch (e) {
      this.$q.notify({
        message: e,
        color: 'red',
        position: "top-right"
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