<template>
  <q-page padding>
    <q-table
      title="Teachers"
      :data="teachers"
      :columns="[
      {name: 'id', label: 'ID', field: 'id', align: 'left'},
      {name: 'name', label: 'NAME', field: 'name', align: 'left'},
      {name: 'email', label: 'EMAIL', field: 'email',align: 'left'},
      ]"
      row-key="name"
    >
    <template v-slot:top>
      <div class="row justify-between table-header">
        <div class="text-h6">Teachers</div>
        <div>
          <q-btn style="background: #FF0080; color: white" label="Invite Teacher" @click="createInviteUrl" />
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

      <q-dialog v-model="showLink">
      <q-card>
        <q-card-section>
          <div class="text-h6">Invite Link</div>
        </q-card-section>

        <q-card-section class="q-pt-none text-body2">
          {{inviteLink}}
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="OK" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { Observer } from 'mobx-vue'
import { Teacher } from 'components/models'

@Observer
@Component
export default class PageTeacher extends Vue {
  teachers: Teacher[] = []

  showLink = false
  inviteLink = ''

  constructor() {
    super()
    this.loadTeacher()
  }
  async loadTeacher() {
    let res = await this.$axios.get(`/api/schools/${this.$route.params.school_id}/teachers`)
    this.teachers = res.data.data
  }

  async createInviteUrl() {
    let res = await this.$axios.get(`/api/schools/${this.$route.params.school_id}/invite_url`)
    this.inviteLink = res.data
    this.showLink = true
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
