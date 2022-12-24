<template>
  <v-select v-model="contentPerPage" width="100px" dense outlined hide-details :label="this.$trans('app.item_per_page')" :items="perPageChoices" @change="changePage">
    <template v-slot:nota-data>
     {{ textNoData }}
    </template>
  </v-select>
</template>
<script>
export default {
  props: { perPage : { type: Number, default : 5}, changePage : { type: Function } },
	data() {
    return {
      perPageChoices : [ {text:'5' , value: 5}, {text:'10' , value: 10}, {text:'20' , value: 20} ],
      contentPerPage: this.perPage
    }
  },
  methods: {
    updatePerpage () {
      this.$emit('update-perpage', { newPerPage: this.contentPerPage })
    }
  },
  watch: {
    perPage (newPerPage) { this.contentPerPage = newPerPage }
  }
}
</script>
<style>
.theme--light.v-select .v-select__selections{
  color: rgba(94,86,105,.87)
}
.select {
  width: 60px;
}
</style>
