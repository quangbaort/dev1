<template>
  <div class="">
    <perfect-scrollbar @ps-scroll-y="onScroll" ref="scrollbar"  :settings="perfectScrollbarSettings">
      <v-treeview @input="changeDepartment" selectable :style="{'width' : `${computedWidth}px`}" :items="listBoard" open-on-click activatable :open="initiallyOpen">
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
    changeDepartment: {
      type : Function,
    }
  },
  data() {
    return {
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
      nextId: 1000
    }
  },
  methods : {
    rename(value , id){
      this.idFolder = 0
    },
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
    edit(item) {
      this.idEdit = item.id
      this.idCreate = item.id
      this.folder = item
    },
    changeElementInput() {
      this.idFolder = this.idEdit
      this.statusInput = true
    },
    createFolder(){
      this.addChild(this.folder)
    },
    addChild(item) {
      if (!item.children) {
        this.$set(item, "children", []);
      }

      const name = `${item.name} (${item.children.length})`;
      const id = this.nextId++;
      item.children.push({
        id,
        name
      });
      this.idFolder = id
      this.statusInput = true
    },
  },
  setup() {
    // ------------------------------------------------
    // calendarOptions
    // ------------------------------------------------
    const calendarOptions = computed(() => store.state['app-calendar'].calendarOptions)

    // ------------------------------------------------
    // selectedCalendars
    // ------------------------------------------------
    const selectedCalendars = computed({
      get: () => store.state['app-calendar'].selectedCalendars,
      set: val => {
        store.commit('app-calendar/SET_SELECTED_CALENDARS', val)
      },
    })
    const initiallyOpen = ref(['public'])
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 1,
      wheelSpeed: 0.7,
    }

    // ------------------------------------------------
    // checkAll
    // ------------------------------------------------
    const checkAll = computed({
      /*
      GET: Return boolean `true` => if length of options matches length of selected filters => Length matches when all events are selected
      SET: If value is `true` => then add all available options in selected filters => Select All
           Else if => all filters are selected (by checking length of both array) => Empty Selected array  => Deselect All
    */
      get: () => selectedCalendars.value.length === calendarOptions.value.length,
      set: val => {
        if (val) {
          selectedCalendars.value = calendarOptions.value.map(i => i.label)
        } else if (selectedCalendars.value.length === calendarOptions.value.length) {
          selectedCalendars.value = []
        }
      },
    })

    return {
      icons: {
        mdiPlus
      },
      calendarOptions,
      selectedCalendars,
      checkAll,perfectScrollbarSettings, initiallyOpen
    }
  },
}
</script>

<style>
.v-navigation-drawer__content{
  overflow-y: auto;
}
</style>
