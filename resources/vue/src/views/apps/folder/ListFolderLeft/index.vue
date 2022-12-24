<template>
  <div class="h-full">
    <!-- Compose -->
    <h4 class="md2 mt-6" @contextmenu.prevent="createFolderRoot">
      <v-icon>
      {{ icons.mdiFolderOutline }}
      </v-icon>
      {{ this.$trans('app.root') }}
    </h4>
    <!-- Scrollable Area -->
      <perfect-scrollbar @ps-scroll-y="onScroll" ref="scrollbar"  :settings="perfectScrollbarSettings"  class="ml-2" style="height: calc(100% - 10px);">
      <v-treeview item-key="path" return-object @update:active="changeFolder" :style="{'width' : `${computedWidth}px`}"
                  :items="listFolder" activatable :open.sync="openIds">
        <template slot="label" slot-scope="{ item , open}" >
          <v-row v-if="statusChange">
            <v-col md="1" sm="1" cols="1">
              <v-icon v-if="item.path != idFolder">
                {{ open ? icons.mdiFolderOpenOutline : icons.mdiFolderOutline }}
              </v-icon>
              <v-icon v-if="item.path == idFolder && statusInput" class="pt-1">
                {{ open ? icons.mdiFolderOpenOutline : icons.mdiFolderOutline }}
              </v-icon>
            </v-col>
            <v-col class="pd-2">
              <span class="px-2 py-1 align-self-center d-inline-block" v-if="item.path !== idFolder" @contextmenu.prevent="changeElementInput(item)"  v-contextmenu:contextmenu="{ trigger : 'click' }">
                {{  truncate(item.name, 16) }}
              </span>
              <v-text-field class="px-1" @keyup.enter="rename($event)" @change="rename($event)"
                            dense outlined v-if="item.path == idFolder && statusInput" :value="item.name" autofocus
              ></v-text-field>
            </v-col>
          </v-row>
          <div v-else>
            <v-icon>
              {{ open ? icons.mdiFolderOpenOutline : icons.mdiFolderOutline }}
            </v-icon>
            <span class="px-1 py-1">{{ item.name }}</span>
          </div>
        </template>
      </v-treeview>
      </perfect-scrollbar>
    <v-contextmenu ref="contextmenu" :style="styleContextMenu" v-if="readOnly">
      <v-contextmenu-item :style="styleContextMenuItem">
        <v-btn color="error" class="w-100" @click="createFolder" small style="background: #30529e;width: 100%">
          {{ this.$trans('app.create_folder') }}</v-btn>
      </v-contextmenu-item>
      <v-contextmenu-item :style="styleContextMenuItem">
        <v-btn color="primary" class="w-100" small @click="edit" style="background: #30529e;width: 100%">{{ this.$trans('app.edit') }}</v-btn>
      </v-contextmenu-item>
      <v-contextmenu-item :style="styleContextMenuItem" @click="openConfirm">
        <v-btn color="error" class="w-100" small style="background: #ff4c51;width: 100%">{{ this.$trans('app.delete') }}</v-btn>
      </v-contextmenu-item>
    </v-contextmenu>
    <confirm-delete :onDelete="deleteFolder" :module="this.$trans('app.archive')" :dialogs="dialogs"></confirm-delete>
  </div>
</template>

<script>
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
import { directive, Contextmenu, ContextmenuItem } from "v-contextmenu";
import {
 mdiFolderOpenOutline,mdiFolderOutline
} from '@mdi/js'
import { ref, computed } from '@vue/composition-api'
import useAppConfig from "../../../../@core/@app-config/useAppConfig";
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import ConfirmDelete from '../../../../common/components/ConfirmDelete.vue';
import store from '@/store'

export default {
  directives: {
    contextmenu: directive,
  },
  components: {
    PerfectScrollbar,
    ConfirmDelete,
    [Contextmenu.name]: Contextmenu,
    [ContextmenuItem.name]: ContextmenuItem,
  },
  computed: {
    computedWidth: function () {
      return this.width;
    }
  },
  props: {
    listFolder : Array,
    statusChange: {
      type : Boolean,
      default : false
    },
    changeFolder: {
      type: Function,
    }
  },
  data() {
    return {
      width : 260,
      statusEdit : false,
      showMenu : false,
      widthPlus  : 50,
      resultWith : 0,
      statusInput : false,
      idFolder : 0,
      folder : null,
      openIds: [],
      dialogs : {
        dialog : false,
      },
      pathFolder : null,
      action  : null,
    }
  },
  methods : {
    openConfirm(){
      this.dialogs.dialog = true;
    },
    deleteFolder(){
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING', true);
        store.dispatch('fileManger/deleteFolder',{
          path : this.folder.path,
        }).then(() => {
          store.commit('TOGGLE_LOADING', false);
          this.dialogs.dialog = false;
          this.$emit('refetch-data');
        })
      }
    },
    rename(name){
      if(name == ''){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message :  trans('message.required_input', {':field' : trans('app.name')})
        })
        return
      }
      if(this.action == 'rename'){
          if(!store.state.loading) {
            store.commit('TOGGLE_LOADING', true)
            store.dispatch('fileManger/renameFolder', {
              name : name,
              path : this.folder.path,
            }).then((res) => {
              store.commit('TOGGLE_LOADING', false)
              this.$emit('refetch-data')
              this.action = null
          })
        }
      }else {
        if(!store.state.loading){
          store.commit('TOGGLE_LOADING', true)
          store.dispatch('fileManger/createFolder', {
            name : name,
            parent_id : this.folder ? this.folder.path: null,
          }).then((res) => {
            store.commit('TOGGLE_LOADING', false)
            this.$emit('refetch-data')
            this.action = null
          })
        }
      }
      this.idFolder = 0
    },
    onScroll(event) {
      event.scrollbarXActive = true
      event.containerWidth = 100
    },
    changeElementInput(item) {
      this.folder = item
    },
    edit() {
      if(this.readOnly){
        this.statusChange = true
        this.statusInput = true
        this.idFolder = this.folder.path
        this.action = 'rename'
      }else{
        return
      }
    },
    createFolderRoot(){
      if(this.readOnly){
        this.folder = null
        if(this.statusChange) {
          this.addChild(this.folder)
        }
      }else {
        return
      }
    },
    createFolder(){
      if(this.readOnly){
        this.addChild(this.folder)
      }else {
        return
      }
    },
    addChild(item) {
      this.openIds.push(item)
      let name = ''
      let path = ''
      this.idFolder = path
      if (item) {
        item.children.push({ name, path })
      }
      else {
        this.listFolder.push({ name, path })
        this.folder = item
      }
      this.statusInput = true
      this.statusChange = true
    },
    truncate(value, size) {
      if (value && this.countLength(value) > size) {
        value = value.substring(0, size-3) + '...';
      }
      return value
    },
    countLength(str) {
      let r = 0;
      for (let i = 0; i < str.length; i++) {
        const c = str.charCodeAt(i);
        // Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
        // Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
        if ( (c >= 0x0 && c < 0x81) || (c === 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
          r += 1;
        } else {
          r += 2;
        }
      }
      return r;
    }
  },
  setup() {
    const { isDark } = useAppConfig()
    const styleContextMenu = ref({})
    const styleContextMenuItem = ref({})
    if(isDark.value){
      styleContextMenu.value = {
        background : '#28243d',
        color : '#fff'
      }
      styleContextMenuItem.value = {
        color : '#fff'
      }
    }
     const readOnly = computed(() => {
      if(store.state.user.role){
        return store.state.user.role == 1 ? true : false
      }else if(store.state.user.is_super_admin  == true){
        return true
      }
    })
    const tree = ref([])
    const initiallyOpen = ref(['public'])
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 1,
      wheelSpeed: 0.7,
    }

    return {
      styleContextMenuItem,
      isDark,
      styleContextMenu,
      readOnly,
      icons: {
        mdiFolderOutline,mdiFolderOpenOutline
      },
      // Perfect Scrollbar
      perfectScrollbarSettings,tree,initiallyOpen
    }
  },
}
</script>

<style lang="scss">
@import '~vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css';
.v-navigation-drawer__content {
  overflow: auto;
}
.v-contextmenu-item{
  text-align: center !important;
}
.v-btn__content {
  font: revert;
}
.v-navigation-drawer{
  width: 260px !important;
}
.email-compose-dialog {
  align-self: flex-end !important;
}
.fill-width {
  overflow-x: auto;
  flex-wrap: nowrap;
}
.v-text-field__details{
  display: none;
}


</style>
