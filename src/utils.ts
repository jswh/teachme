export function isValidEmail(email: string) {
  return /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email)
}
export const AppConfig = {
  oauth_client_id: 4,
  oauth_client_key: 'biWlaDZzHX455wKZ7niJcdlZmilzhVtGr2Yj8PiH',
  // oauth_client_key: 'czAtgJI8cNctZ6rzQuA4RREBgmVC21Fi1zxdcufZ',
  api_base: 'https://teachmenow.herokuapp.com',
  // api_base: 'http://127.0.0.1:8000',
  line_login: {
    response_type: 'code',
    client_id: '1655551351',
    state: 'asdferh',
    scope: 'profile openid'
  }
}
export function serializeQuery(obj: { [key: string]: string }) {
  const str = []
  for (const p in obj) {
    str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]))
  }
  return str.join('&')
}
