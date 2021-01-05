import { promisify } from 'util'
import { createClient } from 'redis'
const client = createClient({
  url: process.env.REDIS_URL + '/0'
});

client.on("error", function (error) {
  console.error(error);
});

const get = promisify(client.get).bind(client)
const setex = promisify(client.setex).bind(client)

export default {
  get,
  setex
}
