import { observable, action } from 'mobx'
import AuthUser from './auth-user'
export class Store {
  @observable user = new AuthUser()
  @observable menus: any[] = [
    {
      title: 'School',
      icon: 'school',
      link: '/school'
    }
  ]

  school_id = ''

  @action.bound usePrincipalMenu() {
    this.menus = [
      {
        title: 'School',
        icon: 'school',
        link: '/school'
      }
    ]
  }

  @action.bound useSchoolMenu() {
    this.menus = [
      {
        title: 'Teacher',
        icon: 'teacher',
        link: `/school/${this.school_id}/teacher`
      },
      {
        title: 'Student',
        icon: 'student',
        link: `/school/${this.school_id}/student`
      }
    ]
  }
}
export default new Store()
