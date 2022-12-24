<template>
  <div class="h-full">
    <!-- Compose -->
    <div class="pt-5 pb-3 px-5" :class="{'active': isAddClass }" v-on:click="addClass" v-click-outside="removeClass" @click="getAll" style="cursor:context-menu">{{name}}</div>
    <!-- Scrollable Area -->
    <perfect-scrollbar :settings="perfectScrollbarSettings" class="ps-email-left-sidebar ml-3">
      <v-treeview :items="listBoard" item-key="id"  activatable open-all style="width: 500px"
                  @update:active="showChildren" :active="treeDepartment"></v-treeview>
    </perfect-scrollbar>
  </div>
</template>

<script>
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import { ref } from '@vue/composition-api'
// Local Components
export default {
  components: {
    PerfectScrollbar,
  },
  props: {
    listBoard : {
      type: Array,
      required: true,
    },
    showChildren : {
      type: Function,
      required: true,
    },
    getAll:{
      type: Function,
      required: true,
    },
    treeDepartment:{
      required: true,
    }
  },
  data(){
    return {
      isAddClass: false,
    }
  },
  methods:{
    addClass() {
      this.isAddClass = true
    },
    removeClass() {
      this.isAddClass = false
    }
  },
  setup() {
    const name = JSON.parse(localStorage.getItem('Organization')).name
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
      wheelSpeed: 0.7,
    }
    const idSelected = ref([])
    return {
      // Perfect Scrollbar
      perfectScrollbarSettings, idSelected, name
    }
  },
}
</script>

<style lang="scss">
@import '~@core/preset/preset/mixins.scss';

.ps-email-left-sidebar {
  height: calc(100% - 78px);

  .v-list--dense .v-list-item {
    height: 38px;
    min-height: 38px;
    border-left: 2px solid transparent;

    &.v-list-item--exact-active {
      border-color: var(--v-primary-base);
    }

    .v-list-item__icon {
      align-items: end;
    }

    // label dot
    .label-dot {
      .v-badge__badge {
        width: 12px !important;
        height: 12px !important;
        border-radius: 10px;
      }
    }
  }
}

.email-compose-dialog {
  align-self: flex-end !important;
}
.active{
  color: #30529e;
  background-color: #e7ebf4;
}

@include app-elevation(email-compose-dialog, 16);
</style>
