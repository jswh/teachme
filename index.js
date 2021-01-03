const express = require('express')
const app = express()
const server = require('http').createServer(app);
const redis = require("redis").createClient({
  url: process.env.REDIS_URL
});
redis.on("error", function (error) {
  console.error(error);
});

const socketRepo = {}
const port = process.env.PORT || 3000
const options = {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
};
const io = require('socket.io')(server, options);

io.on('connection', socket => {
  console.log('connected:' + socket.id)
  socket.on("is_auth", (uid) => {
    redis.set(uid, 'verified')
    let res = redis.get(uid)
    if (res) {
      console.log('authed:' + uid + ':' + socket.id)
      redis.setex(uid, 600, socket.id, (err, reply) => {
        socketRepo[socket.id] = socket
        setTimeout(() => {
          socket.emit("is_auth_ok", 'ok')
        }, 1000);
      })
    } else {
      socket.disconnect()
    }

  })
  socket.on("message", (from, to, text) => {
    console.log(from + '->' + to + ':' + text)
    toSocketId = redis.get(to, function (error, toSocketId) {
      socketRepo[toSocketId].emit("message", from, text)
    })
  });

  socket.on("disconnect", () => {
    console.log('disconnected:' + socket.id)
    if (socketRepo[socket.id]) {
      delete socketRepo[socket.id]
    }
  })
});

app.get('/ping', (req, res) => {
  res.send('pong!!')
})

server.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`)
})
