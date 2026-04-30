// Vue & System related
import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import VueAxios from 'vue-axios'
import {createI18n} from "vue-i18n";
import axios from 'axios'
import '@mdi/font/css/materialdesignicons.css';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import customParseFormat from 'dayjs/plugin/customParseFormat'
import dayjs from "dayjs";
import DayJsAdapter from '@date-io/dayjs'
import {nl as nlDayJs} from 'dayjs/locale/nl'
import {en as enDayJs} from 'dayjs/locale/en'

// Vuetify related
import {VDateInput} from 'vuetify/labs/VDateInput'
import {createVuetify} from 'vuetify'
import * as directives from 'vuetify/directives'
import {aliases, mdi} from 'vuetify/iconsets/mdi'
import {nl, en} from 'vuetify/locale'

// Application specific
import enLang from '../lang/en.json';
import nlLang from '../lang/nl.json';

// Custom theming
import '../sass/variables.scss';
import Layout from "./Shared/Layout.vue";
import {ZiggyVue} from "ziggy-js";

const i18n = createI18n({
  locale: 'nl',
  fallbackLocale: 'nl',
  legacy: false,
  messages: {
    nl: nlLang,
    en: enLang,
  }
});

const vuetify = createVuetify({
  components: {
    VDateInput
  },
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    }
  },
  date: {
    adapter: DayJsAdapter,
    locale: {
      nl: nlDayJs,
      en: enDayJs
    },
  },
  locale: {
    locale: 'nl',
    fallback: 'en',
    messages: {nl, en},
  },
});

createInertiaApp({
  id: 'robijnsbos-app',
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', {eager: true});
    let page = pages[`./Pages/${name}.vue`];

    page.default.layout = page.default.layout || Layout;

    return page
  },
  setup({el, App, props, plugin}) {
    // always use the current URL for Ziggy
    Ziggy.url = window.location.protocol + '//' + window.location.hostname;
    if (window.location.port) {
      Ziggy.url += ':' + window.location.port;
    }

    const app = createApp({render: () => h(App, props)})
      .use(plugin)
      .use(vuetify)
      .use(i18n)
      .use(VueAxios, axios)
      .use(ZiggyVue, Ziggy);

    app.config.globalProperties.$filters = {
      truncate: function (value, num) {
        return String(value).substring(0, num);
      }
    }

    app.config.globalProperties.$route = route;
    app.mount(el);

    // add DayJS customParseFormat
    dayjs.extend(customParseFormat);

    return app;
  },
}).then(r => {
  console.log('Application Booted')
});
