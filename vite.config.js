import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import fs from 'fs';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

const isDocker = process.env.DOCKER === 'true';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.jsx',
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    server: {
        host: '0.0.0.0',
        watch: {
            usePolling: true,
        },
        port: 5173,
        ...(isDocker
            ? {
                  https: {
                      key: fs.readFileSync('/etc/nginx/certs/st.sso.dev-key.pem'),
                      cert: fs.readFileSync('/etc/nginx/certs/st.sso.dev.pem'),
                  },
                  hmr: {
                      protocol: 'wss',
                      host: 'st.sso.dev',
                      port: 5173,
                  },
              }
            : {
                  https: false, // Không bật HTTPS khi chạy cục bộ
                  hmr: {
                      protocol: 'ws', // Dùng WebSocket thường
                  },
              }),
        strictPort: true,
        cors: true,
    },
    preview: {
        https: isDocker,
    },
});
