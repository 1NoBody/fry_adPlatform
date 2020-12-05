import { auth,findAll,audit,findById } from '@/api/company'



const actions = {
  // company auth
  auth({ commit }, companyInfo) {
    return new Promise((resolve, reject) => {
      var {company,lxrname,lxrphone,lxremail,wechat,number,img_url} = companyInfo;
      auth({
        company: company,
        name: lxrname,
        telephone: lxrphone,
        vx: wechat,
        email: lxremail,
        license:  number,
        license_img: img_url}).then(response => {   
          resolve(response) 
      }).catch(error => {
        console.log(error);
        reject(error)
      })
    })
  },
  // company query
  findAll({ commit }, pageInfo) {
    return new Promise((resolve, reject) => {
      var {num,page} = pageInfo;
      findAll({
        num: num,
        page: page
        }).then(response => {   
          resolve(response) 
      }).catch(error => {
        console.log(error);
        reject(error)
      })
    })
  },
  // company query
  findById({ commit }, id) {
    debugger
    return new Promise((resolve, reject) => {
      findById(id).then(response => {   
          resolve(response) 
      }).catch(error => {
        console.log(error);
        reject(error)
      })
    })
  },
  // company audit
  audit({ commit }, auditInfo) {
    return new Promise((resolve, reject) => {
      audit({
        id: auditInfo.id,
        audit: auditInfo.audit,
        reason: auditInfo.reason
        }).then(response => {   
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

