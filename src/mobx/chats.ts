import { observable, action } from 'mobx'
import { Student, Teacher } from '../components/models'

export interface Chat {
  with: Teacher | Student;
  messages: {
    from: number,
    text: string
  }[];
}

export class Chats {
  @observable chats: { [id: number]: Chat } = {}

  @action.bound addChat(user: Teacher | Student) {
    this.chats[user.id] = {
      with: user,
      messages: []
    }
  }

  @action.bound removeChat(user: Teacher | Student) {
    delete this.chats[user.id]
  }

  @action.bound addMessge(from: number, to: number, text: string) {
    this.chats[to].messages.push({ from, text })
  }
}
