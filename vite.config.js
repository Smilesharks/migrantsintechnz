import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ command, mode }) => {
    const env = loadEnv(mode, process.cwd(), "");
    return {
        build: {
            rollupOptions: {
                output: {
                    manualChunks: undefined, // Elimina la separación de chunks
                },
            },
        },
        plugins: [
            tailwindcss(),
            laravel({
                refresh: true,
                input: ["resources/css/site.css", "resources/js/site.js"],
            }),
        ],
        server: {
            open: env.APP_URL,
        },
    };
});
