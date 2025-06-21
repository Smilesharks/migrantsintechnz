import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import focus from "@alpinejs/focus";
import morph from "@alpinejs/morph";
import persist from "@alpinejs/persist";
import precognition from "laravel-precognition-alpine";

// Define el store global antes de Alpine
document.addEventListener("alpine:init", () => {
    // Store para el esquema de colores del sistema
    Alpine.store("colorScheme", {
        preferred: window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light",
        init() {
            // Escucha cambios en el esquema de colores del sistema
            window
                .matchMedia("(prefers-color-scheme: dark)")
                .addEventListener("change", (e) => {
                    this.preferred = e.matches ? "dark" : "light";
                });
        },
    });

    // Inicializa el store
    Alpine.store("colorScheme").init();
});

// Call Alpine.
window.Alpine = Alpine;
Alpine.plugin([collapse, focus, morph, persist, precognition]);
Alpine.start();
