import * as express from "express";
import redis from './Redis'
import { createServer, Server as HttpServer } from 'http'
import { Socket } from 'socket.io';

export default class Server {
  express!: express.Application
  httpServer!: HttpServer
  socketio!: Socket


  socketRepo: { [sid: string]: Socket } = {}
  constructor() {
    let options = {
      cors: {
        origin: "*",
        methods: ["GET", "POST"]
      }
    };
    const app = express()
    app.use(express.json()) // for parsing application/json
    app.use(express.urlencoded({ extended: true })) // for parsing application/x-www-form-urlencoded
    this.express = app
    this.httpServer = createServer(this.express)
    this.socketio = require('socket.io')(this.httpServer, options)

    this.socketio.on('connection', this.onConnection.bind(this))
  }

  onConnection(socket: Socket) {
    console.log('connected:' + socket.id)
    socket.on("is_auth", this.onAuth.bind({ socket, server: this }))
    socket.on("message", this.onMessage.bind(this))
    socket.on("disconnect", () => {
      console.log('disconnected:' + socket.id)
      if (this.socketRepo[socket.id]) {
        delete this.socketRepo[socket.id]
      }
    })

  }
  async onAuth(uid) {
    let app = this as unknown as { socket: Socket, server: Server }
    let res = await redis.get(uid)
    if (res) {
      console.log('authed:' + uid + ':' + app.socket.id)
      await redis.setex(uid, 600, app.socket.id)
      app.server.socketRepo[app.socket.id] = app.socket
      setTimeout(() => {
        app.socket.emit("is_auth_ok", 'ok')
      }, 1000);
    } else {
      app.socket.disconnect()
    }

  }
  async onMessage(fromChatId, fromChatName, toChatId, toChatName, text) {
    console.log(`${fromChatId}:${fromChatName}` + '->' + `${toChatId}:${toChatName}` + ' ' + text)
    let toSocketId = await redis.get(toChatId)
    this.socketRepo[toSocketId].emit("message", fromChatId, fromChatName, text)
  }

  async sendNotification(toChatId, message) {
    let toSocketId = await redis.get(toChatId)
    if (this.socketRepo[toSocketId]) {
      console.log('notification->' + `${toChatId}` + ' ' + message)
      this.socketRepo[toSocketId].emit("notification", message)
    }
  }

  start() {
    const port = process.env.PORT || 3000

    this.express.get('/ping', () => {
      return 'pong!'
    })

    this.express.post('/notification', (req, res) => {
      if (req.body.secret === process.env.NOTIFICATION_SECRET) {
        this.sendNotification(req.body.to_chat_id, req.body.message)
      }
      res.send('ok')
    })

    this.httpServer.listen(port, () => {
      console.log(`Example app listening at http://localhost:${port}`)
    })

  }
}
