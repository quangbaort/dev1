<template>
  <div>
    <v-select :placeholder="this.$trans('app.operate')" :items="selectHandel" @change="changeAction" v-model="actionHandel"
    item-value="value" item-text="title" outlined clearable dense hide-details >
      <template #item="data">
          <v-icon size="20" class="me-2">{{data.item.icon}}</v-icon>
          {{ data.item.title }}
      </template>
    </v-select>
  </div>
</template>
<script>
export default {
    props : ['selectHandel'],
    methods : {
        changeAction (){
            this.$emit('changeAction', this.actionHandel)
        }
    },
    data() {
        return{
            actionHandel: {}
            }
    }
}
</script>
