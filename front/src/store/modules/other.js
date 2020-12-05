import { uploadImg } from '@/api/other'



const actions = {
  // upload img
  uploadImg({ commit }, imgInfo) {
    return new Promise((resolve, reject) => {
      uploadImg({img: imgInfo}).then(response => {   
        resolve(response) 
      }).catch(error => {
        console.log(error);
        reject(error)
      })
    })
  },
}

export default {
  namespaced: true,
  actions
}

