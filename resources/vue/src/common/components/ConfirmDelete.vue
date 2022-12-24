<template>
  <v-dialog v-model="dialogs.dialog" width="500" persistent>
    <v-card>
        <v-card-title class="headline lighten-2 mb-3" primary-title> {{module}} {{ title }} </v-card-title>
        <v-card-text> 選択された{{module}}を削除してもよろしいですか？ </v-card-text>
<!--      <v-card-text> 選択されたファイルを削除してもよろしいですか？</v-card-text>-->
      <v-divider></v-divider>
        <v-card-actions>
          <v-row class="justify-end my-2">
            <v-btn color="default" class="mx-2" @click="close">{{this.$trans('app.close')}}</v-btn>
            <v-btn v-if="!deleteMulti" color="error" class="mx-2" @click="onDelete">{{this.$trans('app.delete')}}</v-btn>
            <v-btn v-if="deleteMulti" color="error" class="mx-2" @click="deleteSelected">{{this.$trans('app.delete')}}</v-btn>
          </v-row>
        </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props:{
    dialogs: { type: Object, default: {}, require: true },
    onDelete: { type: Function },
    module: { type: String, default: '', require : false },
    deleteMulti: { type: Boolean },
    deleteSelected: { type: Function },
    title: { type: String, default: trans('app.delete') },
  },
  methods:{
    close() {
      this.dialogs.dialog = false
      this.$emit('close')
    }
  }
}
</script>
