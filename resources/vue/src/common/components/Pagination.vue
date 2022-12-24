<template>
  <v-pagination v-model="contentCurrentPage" class="my-4" :length="lengthPage" :total-visible="7" @input="changePage"
  ></v-pagination>
</template>
<script>
  export default {
    props: {
      currentPage : { type: Number, default: 1 , required: true}, lengthPage : { type: Number, default: 1},
      changePage : { type : Function , default: () => 1 },
    },
    data() {
      return { contentCurrentPage : this.currentPage }
    },
    methods: {
      updatePage() { this.$emit('update-page' , { newPage : this.contentCurrentPage })}
    },
    watch: {
      currentPage(newPage) { this.contentCurrentPage = newPage }
    }
  }
</script>
<style>
.v-application .my-4{
  margin-top: 0 !important;
  margin-bottom: 0 !important;
}
</style>
