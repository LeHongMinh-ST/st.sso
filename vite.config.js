import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import fs from 'fs';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

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
        host: '0.0.0.0', // Để cho phép truy cập từ bên ngoài
        watch: {
            usePolling: true,
        },
        port: 5173,
        https: {
            key: fs.readFileSync('/etc/nginx/certs/st.sso.dev-key.pem'),
            cert: fs.readFileSync('/etc/nginx/certs/st.sso.dev.pem'),
        },
        strictPort: true,
        cors: true, // Bật CORS để tránh lỗi proxy chặn
        hmr: {
            protocol: 'wss', // Chắc chắn sử dụng WebSocket Secure
            host: 'st.sso.dev',
            port: 5173,
        },
    },
    preview: {
        https: true,
    },
});
