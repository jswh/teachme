import { Chat } from "src/boot/chats"

export interface ChatsStateInterface {
  chats: { [id: string]: Chat }
}

function state(): ChatsStateInterface {
  return {
    chats: {}
  }
}

export default state
