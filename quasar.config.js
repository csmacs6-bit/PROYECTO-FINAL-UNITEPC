/* eslint-env node */
const { configure } = require('quasar/wrappers')
const path = require('path')
const frontendConfig = require('./frontend/quasar.config')

module.exports = configure((ctx) => {
  const config = frontendConfig(ctx)
  const extendViteConf = config.build.extendViteConf

  return {
    ...config,
    sourceFiles: {
      rootComponent: 'frontend/src/App.vue',
      router: 'frontend/src/router/index',
      store: 'frontend/src/stores/index',
    },
    build: {
      ...config.build,
      alias: Object.fromEntries(
        ['src', 'components', 'layouts', 'pages', 'assets', 'boot', 'stores'].map(name => [
          name,
          path.resolve(__dirname, 'frontend/src', name === 'src' ? '' : name),
        ])
      ),
      extendViteConf(viteConf) {
        extendViteConf?.(viteConf)
        viteConf.publicDir = path.resolve(__dirname, 'frontend/public')
        viteConf.server.fs.allow.push(__dirname)
      },
    },
  }
})
