<template>
  <q-page padding>
    <q-table
      grid
      title="Teachers"
      :data="teachers"
      :columns="[
      {name: 'id', label: 'ID', field: 'id', align: 'left'},
      {name: 'name', label: 'NAME', field: 'name', align: 'left'},
      {name: 'description', label: 'DESC', field: 'description',align: 'left'},
      ]"
      row-key="name"
    >
    <template v-slot:item="props">
        <div class="q-pa-xs col-xs-12 col-sm-6 col-md-4">
          <q-card>
            <q-card-section class="row justify-between text-h5">
              <strong>{{ props.row.name }}</strong>
              <q-icon @click="() => onFavoriteClick(props.row)" :name="props.row.is_focused ? 'favorite' : 'favorite_border'" color="red"></q-icon>
            </q-card-section>
            <q-separator />
            <q-card-section class="flex flex-center">
              <div>{{ props.row.description }} g</div>
            </q-card-section>
          </q-card>
        </div>
      </template>
    </q-table>
  </q-page>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { Observer } from 'mobx-vue'
import store from '../mobx'
import { Teacher } from 'components/models'

@Observer
@Component({
  beforeCreate () {
    const userInfo = store.user.userInfo
    if (userInfo && userInfo.roles && userInfo.roles !== 'as_student') {
      this.$router.push('/login?scope=as_student')
    }
  }
})
export default class PageTeacher extends Vue {
  teachers: Teacher[] = []

  constructor () {
    super()
    this.loadTeacher()
  }

  async loadTeacher () {
    const res = await this.$axios.get(`/api/schools/${store.user.userInfo?.school_id as string}/teachers`)
    this.teachers = res.data.data
  }

  async onFavoriteClick (teacher:Teacher) {
    const url = `/api/relation/${teacher.id}}/focus`
    if (teacher.is_focused) {
      await this.$axios.delete(url)
    } else {
      await this.$axios.post(url)
    }
    teacher.is_focused = !teacher.is_focused
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
