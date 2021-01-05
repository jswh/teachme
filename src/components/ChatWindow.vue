<template>
  <div class="q-pa-md row items-start q-gutter-md chat-window">
    <q-card class="chat-card" v-if="window">
      <q-splitter
      v-model="splitterModel"
      style="height: 450px"
    >
    <template v-slot:before>
      <q-tabs v-model="current" vertical class="text-teal" >
          <q-tab v-for="(chat, id) in $store.state.Chats.chats" :key="id" :name="id" :label="chat.with.name"/>
      </q-tabs>
    </template>
    <template v-slot:after>
      <div>
        <q-btn round flat icon="close" style="color: #FF0080"  class="close-window-btn" @click="() => window = false"/>
        <div class="message-panel" v-if="current !== 0">
          <q-tab-panels
            style="height:350px;overflow:auto;"
            v-model="current"
            animated
            swipeable
            vertical
            transition-prev="jump-up"
            transition-next="jump-up"
          >
            <q-tab-panel v-for="(chat, id) in $store.state.Chats.chats" :key="id" :name="id">
              <div class="q-pa-md row justify-center">
                <div style="width: 100%; max-width: 400px">

                  <q-chat-message v-for="(msg, i) in chat.messages" :key="i"
                  :text="[msg.message]"
                  :sent="msg.fromChatId !== selfChatId"
                  :name="msg.fromChatId === selfChatId ? 'me' : msg.fromChatName"
                  avatar="https://cdn.quasar.dev/img/avatar5.jpg"
                  />
                </div>
              </div>
            </q-tab-panel>
          </q-tab-panels>
          <div class="row msg-input">
            <q-input outlined bottom-slots v-model="text" label="message">
              <template v-slot:after>
                <q-btn round dense flat icon="send" @click="sendMessage" />
              </template>
            </q-input>
          </div>
        </div>
      </div>
      </template>
    </q-splitter>
    </q-card>
     <q-btn round color="deep-orange" icon="message" v-else class="message-icon" @click="() => this.window = true"/>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import chatService, { Chat } from 'src/boot/chats'

@Component
export default class ChatWindow extends Vue {
  window = false
  text = ''
  current = ''
  splitterModel = 20

  sendMessage() {
    const chat: Chat = this.$store.state.Chats.chats[this.current]
    chatService.sendMessage(chat.with.chatId, chat.with.name, this.text)
    this.text = ''
  }

  get selfChatId() {
    return chatService.user?.chat_id
  }
}
</script>

<style lang="stylus" scoped>
.chat-window
  position fixed
  bottom 5px
  right 5px
  width 500px
  height 500px
  overflow display

.chat-card {
  width 100%
}
.msg-input {
  position absolute
  bottom 5px
  left 5px
  align-items center
}
.close-window-btn {
  position absolute
  top -10px
  right  -10px
  z-index 999
}
.message-icon {
  position fixed
  bottom 50px
  right  50px
  align-items center
}
</style>
