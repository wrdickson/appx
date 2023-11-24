import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    //  be sure to point this to the dev directory we are working in
    //  ( difficult bug with several dev versions )
    proxy: {
      '/api': 'http://localhost/appx/'
    }
  },
  base: ''
})
//  in order to install the app anywhere in the server directory, 
//  we do some things:
//  1. router uses createWebHashHistory.  sorry, that just seems to be a limitation
//  2. base is set to an empty string as above
//  3. all data request urls do NOT start with a slash : 'api/test', NOT '/api/test'
//  4. when building/deploying, the contents of the dist folder
//     go into whichever server subdirectory we are in
//     so . .  file structure looks like this:
//
//    index.html
//    > assets
//    > api
//    > installer [?]
//
//  This way, the built app can be placed ANYWHERE on the deployment
//  server.  Of course, db config stuff needs to be configured correctly
//  either by an install script or a config file
