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
              <div class="action">
                <q-icon @click="() => onFavoriteClick(props.row)" :name="props.row.is_focused ? 'favorite' : 'favorite_border'" color="red"></q-icon>
                <q-icon @click="() => startChat(props.row)" name="message"></q-icon>
              </div>
            </q-card-section>
            <q-separator />
            <q-card-section class="flex flex-center">
              <div>{{ props.row.name }}</div>
            </q-card-section>
          </q-card>
        </div>
      </template>
    </q-table>
    <div><chat-window ref="chatWindow"></chat-window></div>
  </q-page>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { Teacher } from 'components/models'
import chatService from 'src/boot/chats'
import ChatWindow from 'src/components/ChatWindow.vue'

@Component({
  beforeCreate () {
    const userInfo = this.$store.state.AuthUser.userInfo
    if (userInfo && userInfo.roles && userInfo.roles !== 'as_student') {
      this.$router.push('/login?scope=as_student')
    }
  },
  components: { ChatWindow }
})
export default class PageStudentHome extends Vue {
  teachers: Teacher[] = []
  $refs!: {
    chatWindow: ChatWindow
  }

  constructor () {
    super()
    this.loadTeacher()
  }

  async loadTeacher () {
    const res = await this.$axios.get(`/api/schools/${this.$store.state.AuthUser.userInfo?.school_id as string}/teachers`)
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

  startChat(teacher: Teacher) {
    chatService.addChat(teacher.chat_id, teacher.name)
    chatService.refreshState()
    this.$refs.chatWindow.current = teacher.chat_id
    this.$refs.chatWindow.window = true
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
.action {
  cursor pointer
}
</style>
