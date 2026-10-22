/* eslint-env node */
const { configure } = require('quasar/wrappers')
const path = require('path')

module.exports = configure(() => ({
  boot: [],
  css: ['app.scss'],
  extras: ['material-icons'],
  build: {
    vueRouterMode: 'history',
    extendViteConf(viteConf) {
      // Dependencies can resolve from the parent project's node_modules.
      // Allow their icon assets without exposing other parent directories.
      viteConf.server = viteConf.server || {}
      viteConf.server.fs = viteConf.server.fs || {}
      viteConf.server.fs.allow = [
        ...(viteConf.server.fs.allow || [__dirname]),
        path.dirname(require.resolve('@quasar/extras/package.json')),
      ]
    },
  },
  devServer: {
    open: true,
    proxy: { '/api': { target: 'http://localhost/transportes', changeOrigin: true } },
  },
  framework: {
    config: {},
    plugins: ['Notify', 'Dialog'],
  },
}))
