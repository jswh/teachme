<template>
  <q-page padding>
    <q-table
      title="Students"
      :data="students"
      :columns="[
      {name: 'id', label: 'ID', field: 'id', align: 'left'},
      {name: 'name', label: 'NAME', field: 'name', align: 'left'},
      {name: 'username', label: 'USERNAME', field: 'username',align: 'left'},
      ]"
      row-key="name"
    >
    <template v-slot:top>
      <div class="row justify-between table-header">
        <div class="text-h6">Students</div>
        <q-toggle v-model="focusedOnly" label="only focused"/>
        <div>
          <q-btn style="background: #FF0080; color: white" label="Create Student" @click="() => showDialog = true" />
        </div>
      </div>
      </template>
       <template v-slot:body="props">
        <q-tr :props="props">
          <q-td
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            {{ col.value }}
          </q-td>
        </q-tr>
      </template>

      </q-table>

      <q-dialog v-model="showDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Create Student</div>
        </q-card-section>

        <q-card-section class="q-pt-none text-body2">

      <q-form @submit="createStudent" class="q-gutter-md">
        <q-input color="grey-3" label-color="blue" outlined v-model="name" label="Name"
        :rules="[ val => val && val.length > 0 || 'Please input student name']">
          <template v-slot:append>
            <q-icon name="person" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" outlined v-model="username" label="Username"
        :rules="[ val => val && val.length > 0 || 'Please input username']">
          <template v-slot:append>
            <q-icon name="person" />
          </template>
        </q-input>
        <q-input color="grey-3" label-color="blue" outlined v-model="password" label="Password" type="password"
        :rules="[ val => val && val.length > 0 || 'Please input password']">
          <template v-slot:append>
            <q-icon name="password" />
          </template>
        </q-input>
        <div>
          <q-btn label="Submit" type="submit" color="primary"/>
          <q-btn label="Cancel" @click="() => showDialog = false" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { Observer } from 'mobx-vue'
import store from '../mobx'

@Observer
@Component
export default class PageTeacher extends Vue {
  students: any[] = []
  name = ''
  username = ''
  password = ''

  showDialog = false

  focusedOnly = false

  constructor() {
    super()
    this.focusedOnly = false
    this.loadStudent()
  }
  async loadStudent(focused=false) {
    let url = `/api/schools/${this.$route.params.school_id}/students?focused=${focused}`
    let res = await this.$axios.get(url)
    this.students = res.data.data
  }
  @Watch('focusedOnly')
  onFocuseChange() {
    this.loadStudent(this.focusedOnly)
  }

  async createStudent() {
    let res = await this.$axios.post(`/api/schools/${this.$route.params.school_id}/students`, {
      name: this.name,
      username: this.username,
      password: this.password
    })
    this.students.push(res.data)
    this.showDialog = false
  }
}
</script>
<style lang="stylus" scoped>
.table-header {
  width: 100%
}
.q-pt-none {
  overflow-wrap: anywhere;
}
</style>
