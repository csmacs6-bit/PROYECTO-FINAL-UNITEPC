import { createApp } from 'vue'
import { Quasar } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'
import './css/app.scss'

import App from './App.vue'
import router from './router'

createApp(App)
  .use(Quasar, {
    plugins: {},
  })
  .use(router)
  .mount('#q-app')
