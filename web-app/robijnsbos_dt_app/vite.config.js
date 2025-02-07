import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from "vite-plugin-vuetify";
import fs from 'fs';
import {viteStaticCopy} from "vite-plugin-static-copy";

export default defineConfig({
  build: {
    outDir: '../dt.robijnsbos.nl/',
  },
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
      publicDirectory: "../dt.robijnsbos.nl/",
      buildDirectory: "../dt.robijnsbos.nl/",
      refresh: true,
    }),
    viteStaticCopy({
      targets: [
        {
          src: 'node_modules/circle-flags/flags',
          dest: '../dt.robijnsbos.nl/images/circle-flags'
        }
      ]
    }),
    vue({
      template: {
        transformAssetUrls: {
          // The Vue plugin will re-write asset URLs, when referenced
          // in Single File Components, to point to the Laravel web
          // server. Setting this to `null` allows the Laravel plugin
          // to instead re-write asset URLs to point to the Vite
          // server instead.
          base: null,

          // The Vue plugin will parse absolute URLs and treat them
          // as absolute paths to files on disk. Setting this to
          // `false` will leave absolute URLs un-touched so they can
          // reference assets in the public directory as expected.
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
