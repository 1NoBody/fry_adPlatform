import request from '@/utils/request'

/**图片上传 */
export function uploadImg(data) {
  return request({
    url: '/other/uploadimg',
    method: 'post',
    data
  })
}

