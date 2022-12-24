import axios from '@axios'
export default {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getFolders(ctx, {type}) {
      return new Promise((resolve, reject) => {
          axios
              .get('/file-managements/folders/'+type)
              .then(response => resolve(response))
              .catch(errors => reject(errors))
      })
    },
    getFiles(ctx, {type, path, title, keyword, page, limit, sort=''}) {
      return new Promise((resolve, reject) => {
        let url = '/file-managements/files/'+type+'/'+path+'?title='+title+'&keyword='+keyword+'&page='+page+'&limit='+limit+'&sort='+sort
        if(!path){
          url = '/file-managements/files/'+type+'?title='+title+'&keyword='+keyword+'&page='+page+'&limit='+limit+'&sort='+sort
        }
          axios
              .get(url)
              .then(response => resolve(response))
              .catch(errors => reject(errors))
      })
    },
    createFolder(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('file-managements/folders',  queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    renameFolder(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .patch('file-managements/folders',  queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    uploadFile(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('file-managements/files',  queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`file-managements/files/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteFolder(ctx, {path}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`file-managements/folders/${path}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    storageFree(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/storage-free`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
