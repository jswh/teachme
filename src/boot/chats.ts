import { Teacher, Student } from 'src/components/models'
import { io, Socket } from 'socket.io-client'
import { Store } from 'vuex'

export interface Chat {
  with: {
    chatId: string,
    name: string
  };
  messages: {
    fromChatId: string,
    fromChatName: string,
    message: string
  }[];
}

export class ChatService {
  chats: { [id: string]: Chat } = {}
  user?: Teacher | Student
  connected = false
  socket?: Socket
  store?: Store<any>
  onNotification!: any

  setStore(store: Store<any>) {
    this.store = store
  }

  connect(user: Teacher | Student) {
    this.user = user

    if (this.connected) {
      return
    }

    // const socket = io('http://172.24.75.88:3000')
    const socket = io('https://teachmews.herokuapp.com/')
    this.socket = socket

    socket.on('disconnect', this.onDisconnet.bind(this))
    socket.on('message', this.onRecieveMessage.bind(this))
    socket.on('is_auth_ok', this.onAuth.bind(this))
    socket.on('connect', this.onConnect.bind(this))
    socket.on('notification', this.onNotification)
  }

  onConnect() {
    // eslint-disable-next-line @typescript-eslint/restrict-plus-operands
    console.log('chat connected ' + this.socket?.id)
    // eslint-disable-next-line no-unused-expressions
    this.socket?.emit('is_auth', this.user?.chat_id)
  }

  onDisconnet() {
    console.log('disconnected')
    this.connected = false
  }

  onAuth(msg: string) {
    console.log('auth_result', msg)
    this.connected = msg === 'ok'
    if (this.connected && this.user) {
      console.log('authed')
      this.sendMessage(this.user?.chat_id, this.user?.name, 'talk to self')
      try {
        // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
        const chats = JSON.parse(localStorage.getItem('chats:' + this.user.chat_id)!)
        this.chats = chats
        this.refreshState()
      } catch {
        console.log('load chats history error')
      }
    }
  }

  onRecieveMessage(fromChatId: string, fromChatName: string, message: string) {
    console.log('recieve msg', fromChatId, fromChatName, message)
    this.addChatRecord(fromChatId, fromChatName, fromChatId, fromChatName, message)
  }

  sendMessage(toChatId: string, toChatName: string, message: string) {
    const user = this.user
    if (user) {
      console.log('send msg', toChatId, toChatName, message)
      // eslint-disable-next-line no-unused-expressions
      this.socket?.emit('message', user.chat_id, user.name, toChatId, toChatName, message)
      this.addChatRecord(user.chat_id, user.name, toChatId, toChatName, message)
    }
  }

  addChat(chatId: string, chatName: string) {
    if (chatId === this.user?.chat_id) {
      return
    }
    if (!this.chats) {
      this.chats = {}
    }
    if (!this.chats[chatId]) {
      this.chats[chatId] = {
        with: {
          chatId: chatId,
          name: chatName
        },
        messages: []
      }
    }
  }

  addChatRecord(fromChatId: string, fromChatName: string, withChatId: string, withChatName: string, message: string) {
    if (withChatId === this.user?.chat_id) {
      return
    }
    const msg = {
      fromChatId,
      fromChatName,
      message
    }
    this.addChat(withChatId, withChatName)
    this.chats[withChatId].messages.push(msg)
    this.refreshState()
  }

  refreshState() {
    const chats = JSON.stringify(this.chats)
    // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
    localStorage.setItem('chats:' + this.user!.chat_id, chats)
    // eslint-disable-next-line no-unused-expressions
    this.store?.commit('refreshChats', JSON.parse(chats))
  }
}
const chatService = new ChatService()
export default chatService
