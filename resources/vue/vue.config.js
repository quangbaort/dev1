const path = require('path')
const { mergeSassVariables } = require('@vuetify/cli-plugin-utils')

module.exports = {
  publicPath: '/',
  transpileDependencies: ['vuetify'],
  configureWebpack: {
    resolve: {
      alias: {
        '@themeConfig': path.resolve(__dirname, 'resources/js/themeConfig.js'),
        '@core': path.resolve(__dirname, 'resources/js/src/@core'),
        '@axios': path.resolve(__dirname, 'resources/js/src/plugins/axios.js'),
        '@user-variables': path.resolve(__dirname, 'resources/js/src/styles/variables.scss'),
      },
    },
  },
  chainWebpack: config => {
    const modules = ['vue-modules', 'vue', 'normal-modules', 'normal']
    modules.forEach(match => {
      config.module
        .rule('sass')
        .oneOf(match)
        .use('sass-loader')
        .tap(opt => mergeSassVariables(opt, "'@/styles/variables.scss'"))
      config.module
        .rule('scss')
        .oneOf(match)
        .use('sass-loader')
        .tap(opt => mergeSassVariables(opt, "'@/styles/variables.scss';"))
    })
  },
}