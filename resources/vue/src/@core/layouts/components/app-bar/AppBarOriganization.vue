<template>
<div>
  <!-- <v-menu v-if="!role()" offset-y nudge-bottom="22" min-width="175" left:elevation="$vuetify.theme.dark ? 9 : 8" >
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs"  class="d-flex align-center" v-on="on">
        <span v-if="$vuetify.breakpoint.smAndUp">{{ organizationActive.name }}</span>
      </div>
    </template>
    <v-list>
      <v-list-item-group
      >
        <v-list-item v-for="organization in listOrganizations"
          :key="organization.name" :value="organization.name"
        >
          <v-list-item-title @click="changeOrganization(organization)">{{ organization.name }}</v-list-item-title>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </v-menu> -->
  <v-select hide-details="auto" @change="changeOrganization" :label="organizationActive.name" :items="listOrganizations"
    item-text="name" item-value="id" return-object >
   <template v-slot:nota-data>
     {{ textNoData }}
    </template>
  </v-select>
</div>

</template>

<script>
import { registerModule } from '@/common'
import storeModule from './store/storeModule'
import useStore from './store/use_store'
import { role,textNoData } from '@/common'
import store from '@/store'
export default {
  setup() {
    registerModule(storeModule , 'listOrganization')
    const organizationActive = JSON.parse(localStorage.getItem('Organization'))
    const changeOrganization = (organization) => {
      localStorage.setItem('Organization' , JSON.stringify(organization))
      store
      .dispatch('listOrganization/getMenu' , {
        organization_id : organization.id
      }).then(response => {
        localStorage.setItem('userMenu' , JSON.stringify(response.data))
        window.location.reload()
      })
    }
    const { listOrganizations } = useStore()
    return {
      registerModule,listOrganizations,changeOrganization,role,organizationActive
    }
  },
}
</script>
<style scoped>
.custom-placeholder {
  padding : 1rem;
}
</style>
