import { defineConfig, splitVendorChunkPlugin } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import * as path from 'path'

process.env.ASSET_URL = process.cwd().replace(path.resolve('../../../../'), '').replace(/\\/g, '/') + '/public'
//process.env.ASSET_URL = 'public'
// process.env.APP_URL = '/'
console.log(process.env.ASSET_URL)

export default defineConfig({
  root: process.cwd(),
  build: {
    target: 'esnext',
    // rollupOptions: {
    //   output: {
    //     manualChunks (id) {
    //       if (id.includes('node_modules')) {
    //         return id.toString().split('node_modules/')[1].split('/')[0].toString()
    //       }
    //     },
    //     assetFileNames: (assetInfo) => {
    //       let extType = assetInfo.name.split('.').at(1),
    //         ext = assetInfo.name.split('.').pop()
    //
    //       if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType) || /svg/i.test(ext)) {
    //         extType = 'img'
    //       } else if (/webfonts|fonts/i.test(extType) || /ttf|woff2/i.test(ext)) {
    //         extType = 'fonts'
    //       }
    //
    //       return `assets/${extType}/[name].[hash][extname]`
    //     },
    //     chunkFileNames: 'assets/js/[name].[hash].js',
    //     entryFileNames: 'assets/[name].[hash].js'
    //   }
    // }
  },
  plugins: [
    splitVendorChunkPlugin(),
    laravel({
      input: [
        './resources/css/app.css',
        './resources/js/app.js'
      ],
      publicDirectory: 'public',
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: 'public',
          includeAbsolute: false
        }
      }
    })
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
      '@': '/resources/js',
      '@assets': '/resources/js/assets'
    },
    extensions: ['.js', '.vue', '.json']
  }
})
