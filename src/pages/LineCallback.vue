<template>
  <div>
    <div class="fullscreen bg-blue text-white text-center q-pa-md flex flex-center" v-if="bindings.length < 1">
      login...
    </div>
    <div class="q-pa-md row items-start q-gutter-md">
      <q-card class="my-card">
        <q-card-section v-if="teachers.length > 0">
          <div style="text-align: center; margin: 1rem 0;">
            Login As Teacher
          </div>
          <div v-for="teacher in teachers" :key="teacher.name" >
            <q-btn style="width:100%" outline rounded color="primary" :label="teacher.name" @click="() => {login(teacher)}" />
          </div>
        </q-card-section>
        <q-card-section v-if="students.length > 0">
          <div style="text-align: center; margin: 1rem 0;">
            Login As Students
          </div>
          <div  v-for="student in students" :key="student.name" >
            <q-btn style="width:100%" outline rounded color="secondary" :label="student.name" @click="() => {login(student)}" />
          </div>
        </q-card-section>
        <q-btn style="width:100%" outline rounded label="+" to="/login" />
      </q-card>
    </div>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component
export default class PageLineCallback extends Vue {
  constructor() {
    super()
    const urlParams = new URLSearchParams(window.location.search)
    const code = urlParams.get('code')
    console.log(code)
    if (code) {
      this.init(code)
    }
  }

  bindings: any[] = []

  get teachers() {
    const teachers: any[] = []
    this.bindings.forEach(v => {
      if (v.type === 'teacher') {
        teachers.push(v)
      }
    })

    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return teachers
  }

  get students() {
    const students: any[] = []
    this.bindings.forEach(v => {
      if (v.type === 'student') {
        students.push(v)
      }
    })

    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return students
  }

  async init(code: string) {
    const res: any = await this.$axios.get('/api/line/token/' + code)
    sessionStorage.setItem('line_id_token', res.id_token)
    localStorage.setItem('line_info', JSON.stringify(res))
    // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
    const bindings = await this.$axios.post('/api/line/bindings', { id_token: res.id_token })
    if (!bindings.data) {
      this.$router.replace('/login')
    } else {
      this.bindings = bindings.data
    }
  }

  login(binding: any) {
    localStorage.setItem('token', binding.access_token)
    this.$store.commit('setUserInfo', binding.user)
    if (binding.type === 'teacher') {
      this.$router.push(`/school/${binding.user.school_id as string}/teacher`)
    } else {
      this.$router.push('/')
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
