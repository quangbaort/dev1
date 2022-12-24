<template>
  <loader v-if="loading" object="#335cff" color1="#ffffff" color2="#17fd3d" size="10" speed="2" bg="#343a40" objectbg="#999793" opacity="80" disableScrolling="false" name="spinning"></loader>
</template>

<script>
export default {
  props: { loading : { type : Boolean , default : false }, opacity : { type : String, default: '80' } }
}
</script>

<style>
.loader-spinner{
  width: 90px !important;
  height: 90px !important;
}
#overlay-spinner{
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: 99999 !important;
}
</style>
