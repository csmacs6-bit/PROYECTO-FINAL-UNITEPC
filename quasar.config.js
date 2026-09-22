/* eslint-env node */
const { configure } = require('quasar/wrappers')

module.exports = configure(() => ({
  boot: [],
  css: ['app.scss'],
  extras: ['material-icons'],
  build: {
    vueRouterMode: 'history',
  },
  devServer: {
    open: true,
  },
  framework: {
    config: {},
    plugins: ['Notify'],
  },
}))
