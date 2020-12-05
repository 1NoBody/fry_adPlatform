import request from '@/utils/request'

export function get(url,params) {
  return request({
    url: url,
    method: 'get',
    params
  })
}

export function post(url,data) {
    return request({
      url: url,
      method: 'post',
      data  
    })
}