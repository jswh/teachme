<template>
  <q-page padding>
    <q-dialog
      v-model="dialog"
      persistent
      :maximized="maximizedToggle"
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card>
        <q-card-section>
          <div class="text-h6">Create A School </div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <school-apply :onCancel="onApplyCancel" :onSuccess="onApplySuccess"></school-apply>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-table
      title="Schools"
      :data="schools"
      :columns="[
      {name: 'id', label: 'ID', field: 'id', align: 'left'},
      {name: 'name', label: 'NAME', field: 'name', align: 'left'},
      {name: 'description', label: 'DESC', field: 'description',align: 'left'},
      ]"
      row-key="name"
    >
    <template v-slot:top>
      <div class="row justify-between table-header">
        <div class="text-h6">School List</div>
        <div>
          <q-btn style="background: #FF0080; color: white" label="Apply New" @click="() => dialog = true" />
        </div>
      </div>
      </template>
       <template v-slot:body="props">
        <q-tr :props="props" @click="onColClick(props.row)">
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
  </q-page>
</template>

<script lang="ts">
import SchoolApply from 'components/SchoolApply.vue'
import { Vue, Component } from 'vue-property-decorator'
import { School } from 'components/models'

@Component({
  components: { SchoolApply }
})
export default class PageSchool extends Vue {
  dialog = false
  maximizedToggle = true
  schools: School[] = []
  constructor () {
    super()
    this.loadSchool()
  }

  async loadSchool () {
    const res = await this.$axios.get('/api/schools')
    this.schools = res.data
    this.dialog = this.schools.length === 0
  }

  onApplyCancel () {
    this.dialog = false
  }

  onApplySuccess (school: School) {
    this.schools.push(school)
    this.dialog = false
  }

  onColClick (school: School) {
    this.$router.push(`/school/${school.id}/teacher`)
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
