import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from "vite-plugin-vuetify";
import fs from 'fs';
import {viteStaticCopy} from "vite-plugin-static-copy";

export default defineConfig({
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler' // or "modern"
      }
    }
  },
  plugins: [
    laravel({
      input: [
        'resources/js/app.js'
      ],
      refresh: true,
    }),
    viteStaticCopy({
      targets: [
        {
          src: 'node_modules/circle-flags/flags',
          dest: '../images/circle-flags'
        }
      ]
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: true,
        },
      },
    }),
    vuetify({
      autoImport: true,
      styles: {
        configFile: 'resources/sass/settings.scss',
      },
    })
  ],
  server: {
    host: true,
    port: 12013,
    hmr: {
      host: 'robijnsbos.home',
      protocol: 'ws'
    },
  },
  resolve: {
    alias: [
      {
        find: 'vue-i18n',
        replacement: 'vue-i18n/dist/vue-i18n.cjs.js',
      }
    ]
  }
})
