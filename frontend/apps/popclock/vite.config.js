import { fileURLToPath, URL } from 'node:url'

import { defineConfig, searchForWorkspaceRoot } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  base: '/popclockV2/',
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '@widgets': fileURLToPath(new URL('../../widgets/src', import.meta.url)),
      '@shared': fileURLToPath(new URL('../../shared/src', import.meta.url)),
    },
  },
  optimizeDeps: {
    include: ['d3-geo'],
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    fs: {
      allow: [
        searchForWorkspaceRoot(process.cwd()),
        fileURLToPath(new URL('../../widgets/src', import.meta.url)),
        fileURLToPath(new URL('../../shared/src', import.meta.url)),
      ],
    },
    proxy: {
      '/popdata': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/popdata/, ''),
      },
    },
  },
})