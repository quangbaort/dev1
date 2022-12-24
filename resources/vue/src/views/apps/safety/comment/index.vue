<template>
  <v-dialog v-model="dialogs.comment" max-width="400" persistent>
    <v-card>
      <v-card-title class="headline">
        {{ this.$trans('app.comment_title') }}
      </v-card-title>
      <v-card-text class="mb-5">
        {{ comment }}
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="justify-end py-3">
          <v-btn color="default" @click="close()" >{{ this.$trans('app.comment') }}</v-btn>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>

export default {
  props : {
    dialogs : {
      type : Object,
      require : true
    },
    inputComment: {
      type: Object,
    }
  },
  watch: {
    inputComment(){
      this.comment = this.inputComment.comment;
    }
  },

  data() {
    return {
      comment: '',
    }
  },
  methods: {
    close() {
      this.dialogs.comment = false;
      this.currentTab = null
    },
  }

}
</script>
