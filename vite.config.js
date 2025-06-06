import fs from "fs";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

const isDocker = process.env.DOCKER === "true";
const host = process.env.VITE_DEV_SERVER_HOST || "0.0.0.0";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.scss",
                "resources/css/auth.scss",
                "resources/js/app.js",
                "resources/js/auth/login.js",
            ],
            refresh: true,
        }),
    ],
    esbuild: {
        jsx: "automatic",
    },
    server: {
        host: host,
        watch: {
            usePolling: true,
        },
        port: 5173,
        ...(isDocker
            ? {
                  https: {
                      key: fs.readFileSync(
                          "/etc/nginx/certs/st.sso.dev-key.pem",
                      ),
                      cert: fs.readFileSync("/etc/nginx/certs/st.sso.dev.pem"),
                  },
                  hmr: {
                      protocol: "wss",
                      host: "st.sso.dev",
                      port: 5173,
                  },
              }
            : {
                  https: false, // Không bật HTTPS khi chạy cục bộ
                  hmr: {
                      protocol: "ws", // Dùng WebSocket thường
                  },
              }),
        strictPort: true,
        cors: true,
    },
    preview: {
        https: isDocker,
    },
});
