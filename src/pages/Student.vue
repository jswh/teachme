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
        <div class="text-h6">Students <span v-if="isPrincipal" class="helper">click student to send notification</span></div>
        <q-toggle v-if="!isPrincipal" v-model="focusedOnly" label="only focused"/>
        <div v-if="isPrincipal">
          <q-btn style="background: #FF0080; color: white" label="Create Student" @click="() => showDialog = true" />
        </div>
      </div>
      </template>
       <template v-slot:body="props">
        <q-tr :props="props" class="student-line" @click="() => startNotification(props.row)">
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
    <q-dialog v-model="showSendDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">
            Send notification to {{this.toSendUser ? this.toSendUser.name: ''}}
          </div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-form>
            <q-input v-model="toSendMessage" type="textarea" outlined label="message"></q-input>
          </q-form>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn :loading="sending" @click="sendNotification" flat>send</q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { Student } from 'src/components/models'

@Component
export default class PageTeacher extends Vue {
  students: Student[] = []
  name = ''
  username = ''
  password = ''

  showDialog = false
  focusedOnly = false

  toSendUser?: Student | null = null
  showSendDialog = false
  toSendMessage = ''
  sending = false

  constructor () {
    super()
    this.focusedOnly = false
    this.loadStudent()
  }

  get isPrincipal() {
    return this.$store.state.AuthUser.userInfo.roles === 'as_principal'
  }

  async loadStudent (focused = false) {
    // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
    const url = `/api/schools/${this.$route.params.school_id}/students?focused=${focused}`
    const res = await this.$axios.get(url)
    this.students = res.data.data
  }

  startNotification(student: Student) {
    if (this.isPrincipal) {
      this.toSendUser = student
      this.showSendDialog = true
    } else {
      console.log('not principal')
    }
  }

  async sendNotification() {
    this.sending = true
    try {
      await this.$axios.post('/api/notification', {
        to: this.toSendUser?.id,
        message: this.toSendMessage
      })
      this.toSendUser = null
      this.toSendMessage = ''
      this.showSendDialog = false
      this.sending = false
      this.$q.notify('send success')
    } catch (e) {
      this.sending = false
      this.$q.notify('send error')
    }
  }

  @Watch('focusedOnly')
  onFocuseChange () {
    this.loadStudent(this.focusedOnly)
  }

  async createStudent () {
    const res = await this.$axios.post(`/api/schools/${this.$route.params.school_id}/students`, {
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
.student-line {
  cursor pointer
}
.helper {
  font-size 12px
  font-weight 400
}
</style>
