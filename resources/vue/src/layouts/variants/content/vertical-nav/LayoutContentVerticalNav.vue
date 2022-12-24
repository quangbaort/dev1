<template>
  <layout-content-vertical-nav :nav-menu-items="navMenuItems">
    <slot></slot>

    <!-- Slot: Navbar -->
    <template #navbar="{ isVerticalNavMenuActive, toggleVerticalNavMenuActive }">
      <div
        class="navbar-content-container"
        :class="{'expanded-search': shallShowFullSearch}"
      >
        <!-- Left Content: Search -->
        <div class="d-flex align-center">
          <v-icon
            v-if="$vuetify.breakpoint.mdAndDown"
            class="me-3"
            @click="toggleVerticalNavMenuActive"
          >
            {{ icons.mdiMenu }}
          </v-icon>
        </div>

        <!-- Right Content: I18n, Light/Dark, Notification & User Dropdown -->
        <div class="d-flex align-center right-row">
		  <app-bar-origanization></app-bar-origanization>
          <app-bar-theme-switcher class="mx-4"></app-bar-theme-switcher>
          <app-bar-user-menu></app-bar-user-menu>
        </div>
      </div>
    </template>

    <!-- Slot: Footer -->
    <template #footer>
      <div class="d-flex justify-space-between">
        <span>COPYRIGHT &copy; {{ new Date().getFullYear() }} <a
          href="#"
          class="text-decoration-none"
        >Systems Nakashima Co.,Ltd</a><span class="d-none d-md-inline"> All rights Reserved</span></span>
    </div>
    </template>

  </layout-content-vertical-nav>
</template>

<script>
import LayoutContentVerticalNav from '@/@core/layouts/variants/content/vertical-nav/LayoutContentVerticalNav.vue'
import navMenuItems from '@/navigation/vertical'

// App Bar Components
import AppBarThemeSwitcher from '@core/layouts/components/app-bar/AppBarThemeSwitcher.vue'
import AppBarUserMenu from '@core/layouts/components/app-bar/AppBarUserMenu.vue'
import AppBarOriganization from '@core/layouts/components/app-bar/AppBarOriganization.vue'
import { mdiMenu, mdiHeartOutline } from '@mdi/js'

import { getVuetify } from '@core/utils'

// Search Data


import { ref, watch } from '@vue/composition-api'

export default {
  components: {
    LayoutContentVerticalNav,
    // App Bar Components
    AppBarThemeSwitcher,
    AppBarUserMenu,
	  AppBarOriganization,
  },
  setup() {
    const $vuetify = getVuetify()
    // Search
    const appBarSearchQuery = ref('')
    const shallShowFullSearch = ref(false)


    return {
      navMenuItems,

      // Search
      shallShowFullSearch,
      icons: {
        mdiMenu,
        mdiHeartOutline,
      },
    }
  },
}
</script>

<style lang="scss" scoped>
.navbar-content-container {
  height: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-grow: 1;
  position: relative;
}
.v-chip{
  overflow: revert !important;
}

// ? Handle bg of autocomplete for blured appBar

.v-app-bar.bg-blur {
  .expanded-search {
    ::v-deep .app-bar-autocomplete-box div[role='combobox'] {
      background-color: transparent;
    }

    > .d-flex > button.v-icon {
      display: none;
    }

    // ===

    & > .right-row {
      visibility: hidden;
      opacity: 0;
    }

    ::v-deep .app-bar-search-toggler {
      visibility: hidden;
    }
  }
}
</style>
