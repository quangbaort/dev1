const mix = require('laravel-mix')
const path = require('path')

const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
const webpack = {
  output: {
    chunkFilename: 'assets/js/chunks/js.[chunkhash].js',
  },
  resolve: {
    alias: {
      '@': path.resolve('resources/vue/src'),
      "@core" : path.resolve('resources/vue/src/@core'),
      "@axios" : path.resolve('resources/vue/src/plugins/axios.js'),
      "@themeConfig" : path.resolve('resources/vue/themeConfig.js'),
      "@user-variables" : path.resolve('resources/vue/src/styles/variables.scss'),
    },
    fallback: {
      "fs": false,
      "tls": false,
      "net": false,
      "path": false,
      "zlib": false,
      "http": false,
      "https": false,
      "stream": require.resolve("stream-browserify"),
      "crypto": require.resolve("crypto-browserify"),
      "crypto-browserify": require.resolve('crypto-browserify'), //if you want to use this module also don't forget npm i crypto-browserify
    }
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              '@vue/cli-plugin-babel/preset',
            ]
          }
        }
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.scss$/,
        loader: "sass-loader",
        options: {
            additionalData: '@import "./resources/vue/sass/styles/variables.scss";'
        }
      },
    ]
  },
  plugins: [new VuetifyLoaderPlugin()],
}
mix.webpackConfig(webpack)

mix
  .js('resources/vue/app.js', 'public/assets/js')
  .extract()
  .vue();

if (!mix.inProduction()) {
  mix.webpackConfig({
    devtool: 'inline-source-map'
  }).sourceMaps();
}
mix.browserSync({
  proxy: 'netmaster.test',
  open: false,
  watchOptions: {
      usePolling: true,
      interval: 1
  }
});
mix.version()
