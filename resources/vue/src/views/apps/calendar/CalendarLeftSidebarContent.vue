<template>
  <div class="ma-5">
    <v-btn v-if="role" block color="primary" style="width : 100%" @click="$emit('add-event')">
      <v-icon>
        {{ icons.mdiPlus}}
      </v-icon>
      {{this.$trans('app.add_new')}}
    </v-btn>
    <!-- Calendars -->
    <h3 class="mt-5 mb-2">{{organizationActive}}</h3>
    <perfect-scrollbar  @ps-scroll-x="onScroll"  ref="scrollbar"  :settings="perfectScrollbarSettings" style="height: calc(100% - 10px);">
      <v-treeview @input="changeDepartment" :value="departmentIDs" v-model="departmentIDs" selectable selection-type="independent" :style="{'width' : `${computedWidth}px`}" :items="listBoard" open-on-click activatable>
      </v-treeview>
    </perfect-scrollbar>
  </div>
</template>

<script>
import store from '@/store'
import {computed, ref} from '@vue/composition-api'
import { mdiPlus } from  '@mdi/js'
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
export default {
  components: {
    PerfectScrollbar
  },
  computed: {
    computedWidth: function () {
      return this.width;
    }
  },
  props: {
    listBoard : {
      type: Array,
      required: true,
    },
    statusChange: {
      type : Boolean,
      default : false
    },
    changeDepartment : {
      type : Function,
    },
    boardIdArr : {
      type : Array,
      default : []
    },
    role: {
      type: Boolean,
    }
  },
  watch: {
    boardIdArr(val) {
      this.departmentIDs = val
    }
  },
  data() {
    return {
      departmentIDs : [],
      organizationActive : JSON.parse(localStorage.getItem('Organization')).name,
      width : 300,
      statusEdit : false,
      idEdit : null,
      showMenu : false,
      widthPlus  : 50,
      resultWith : 0,
      statusInput : false,
      idFolder : 0,
      idCreate : 0,
      folder : null,
      nextId: 1000,
      listDepart : []
    }
  },
  methods : {
    openDialog(open , item) {
      if(!open && item.children){
        this.resultWith = this.width += this.widthPlus
        this.width = this.resultWith
      }
      else if(open){
        if(this.width <= 256) {
          return
        }
        this.resultWith =  this.width -= this.widthPlus
        this.width = this.resultWith
      }
    },
    onScroll(event) {
      event.scrollbarXActive = true
      event.containerWidth = 100
    },
  },
  setup() {

    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 1,
      wheelSpeed: 0.7,
    }

    // ------------------------------------------------
    // checkAll
    // ------------------------------------------------


    return {
      icons: {
        mdiPlus
      },
      perfectScrollbarSettings
    }
  },
}
</script>

<style>
.v-navigation-drawer__content{
  overflow-y: auto;
}
.v-treeview-node__root{
  padding-left: 0 !important;
}
</style>
