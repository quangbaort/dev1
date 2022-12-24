

require('./overrides.scss')

// Skins
require('./skins/bordered.scss')
require('./skins/semi-dark.scss')
import themeConfig from '@themeConfig'
export default {
  theme: {
    themes: {
      ...themeConfig.themes,
    },
    dark:
      localStorage.getItem('materio-active-theme') === null
        ? themeConfig.app.isDark
        : localStorage.getItem('materio-active-theme') === 'dark',
  },
  rtl: themeConfig.app.isRtl,
}
