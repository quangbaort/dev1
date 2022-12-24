<template>
  <div>
    <v-snackbar v-model="snackbar.isColorSnackbarVisible" top center :color="snackbar.color" v-if="Object.prototype.toString.call(snackbar.message) === '[object Object]'">
        <div v-for="(messages) in snackbar.message" >
          <div v-for="message in messages" v-if="Array.isArray(messages)">
            {{ message }}
          </div>
        </div>
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar.isColorSnackbarVisible = false" >
          <v-icon>{{ icons.mdiClose }}</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
    <v-snackbar v-model="snackbar.isColorSnackbarVisible" top center :color="snackbar.color" v-else>
        {{ snackbar.message }}
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar.isColorSnackbarVisible = false" >
          <v-icon>{{ icons.mdiClose }}</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>

import { mdiClose } from '@mdi/js';
export default {
  props: {
    snackbar : {
      type : Object,
      default : {
        isColorSnackbarVisible : false,
        message : null,
        color : 'error'
      },
      required: true,
    }
  },
  setup() {
    return {
      icons : {
        mdiClose
      }
    }
  }
}
</script>
<style>
.theme--light.v-data-table td {
  color: rgba(0,0,0,.87) !important;
}
</style>
