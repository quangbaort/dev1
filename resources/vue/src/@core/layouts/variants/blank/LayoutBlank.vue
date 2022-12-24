<template>
  <v-app>
    <v-main>
      <slot></slot>
    </v-main>
  </v-app>
</template>

<script>
export default {}
</script>

<style>
@import url('https://fonts.googleapis.com/css?family=M+PLUS+1p" rel="stylesheet');

.v-application,.v-list-item__title,span ,.v-input,.v-label,.v-input input {
  font-family: 'M PLUS Rounded 1c' !important;
  font-size: 14px !important;
}

.v-card {
	padding: 1rem !important;
  border-radius: 6px !important;
}
.v-card__subtitle, .v-card__text, .v-card__title {
  padding: 10px !important;
}
.v-dialog--scrollable > .v-card > .v-card__text, .v-dialog--scrollable > form > .v-card > .v-card__text {
  overflow-x: hidden !important;
}
.theme--light.v-text-field>.v-input__control>.v-input__slot:before {
  border-color: rgba(94,86,105,.14);
}
.theme--dark.v-application{
  background : #28243d !important;
}
::-webkit-scrollbar {

  width: 0;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}
.app-navigation-menu{
  scrollbar-width: none;
}
.v-navigation-drawer__content{
  overflow-y: hidden;
}
/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
.v-footer{
  background-color: transparent !important;
}

.theme--light.v-application{
  background-color: #f4f5fa !important;
}

@media (min-width: 450px){
  button.v-btn.primary,button.v-btn.success,button.v-btn.error ,button.v-btn.default,button.v-btn.warning, button.v-btn--disabled{
    min-width: 120px !important;
  }
}
@media (max-width: 450px){
  button.v-btn.primary,button.v-btn.success,button.v-btn.error ,button.v-btn.default,button.v-btn.warning,button.v-btn--disabled {
    min-width: 50px !important;
  }
  .v-tabs:not(.v-tabs--vertical):not(.v-tabs--right) > .v-slide-group--is-overflowing.v-tabs-bar--is-mobile:not(.v-slide-group--has-affixes) .v-slide-group__prev {
    display: none;
  }


}
.v-tabs--grow > .v-tabs-bar .v-tab{
  flex: revert !important;
}
.theme--dark.v-subheader .title-wrapper span{
  background-color: #312d4b  !important;
}
.theme--dark.v-card,.theme--dark.v-data-table,.theme--dark.v-tabs-items,.theme--dark.v-tabs,
.theme--dark.v-sheet,.theme--dark.v-tabs > .v-tabs-bar,.theme--dark.v-navigation-drawer,
.theme--dark.v-btn.v-btn--has-bg.v-size--default.default,.theme--dark.v-picker__body,.theme--dark.v-btn-toggle:not(.v-btn-toggle--group),
.theme--dark.v-calendar-weekly,.theme--dark.v-calendar-weekly .v-calendar-weekly__day.v-outside,.theme--dark.v-calendar-daily,
.theme--dark.v-calendar-weekly .v-calendar-weekly__head-weekday.v-outside,.theme--dark.v-calendar-events .v-event-more
{
  background-color: #312d4b !important;
}
.v-calendar.v-calendar-events .v-calendar-weekly__day{
  margin-right : 0 !important;
}
.v-list-item{
  padding : 0 25px !important;
}
.v-card .v-card__subtitle:last-child, .v-card .v-card__text:last-child, .v-card .v-card__actions:last-child{
  padding-bottom: 0 !important;
}
.v-input--selection-controls{
  margin-top: 0 !important;
}
.mx-2.v-btn.v-btn--is-elevated.v-btn--has-bg.v-size--default.success,.mx-2.v-btn.v-btn--is-elevated.v-btn--has-bg.v-size--default.warning,.mx-2.v-btn.v-btn--is-elevated.v-btn--has-bg.v-size--default.error{
  box-shadow: 0 7px 8px -4px rgb(94 86 105 / 42%) !important
}
.v-application.theme--light .auth-wrapper.auth-v2 .auth-inner .auth-bg {
    background: #f4f5fa  !important;
}
.v-chip .v-chip__content{
  font-size: 12px !important;
}
.v-calendar-events .v-event-more{
  background-color: revert !important;
}
</style>
