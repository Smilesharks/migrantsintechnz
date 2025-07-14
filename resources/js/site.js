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

    // Store para el theme
    Alpine.store("theme", {
        theme: Alpine.$persist("system").as("theme"),
        set(value) {
            this.theme = value;
        },
        get() {
            return this.theme;
        },
    });

    // Componente themeToggleMobile
    Alpine.data("themeToggleMobile", () => {
        return {
            themeToggleOpen: false,
            preferredColorScheme: window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches
                ? "dark"
                : "light",
            themeColor: false,
            lightThemeColor: "#ffffff",
            darkThemeColor: "#000000",

            themeLight: function () {
                this.$store.theme.set("light");
                this.setLightTheme();
                this.themeToggleOpen = false;
            },
            themeDark: function () {
                this.$store.theme.set("dark");
                this.setDarkTheme();
                this.themeToggleOpen = false;
            },
            themeSystem: function () {
                this.$store.theme.set("system");
                window.matchMedia("(prefers-color-scheme: light)").matches &&
                    this.setLightTheme();
                window.matchMedia("(prefers-color-scheme: dark)").matches &&
                    this.setDarkTheme();
                this.themeToggleOpen = false;
            },
            setLightTheme: function () {
                document.documentElement.classList.remove("dark");
                if (this.themeColor) {
                    const themeColorMeta = document.querySelector(
                        "meta[name=theme-color]"
                    );
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute(
                            "content",
                            this.lightThemeColor
                        );
                    }
                }
            },
            setDarkTheme: function () {
                document.documentElement.classList.add("dark");
                if (this.themeColor) {
                    const themeColorMeta = document.querySelector(
                        "meta[name=theme-color]"
                    );
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute(
                            "content",
                            this.darkThemeColor
                        );
                    }
                }
            },
            root: {
                ["x-init"]() {
                    // Listen for color scheme changes
                    window
                        .matchMedia("(prefers-color-scheme: dark)")
                        .addEventListener("change", (e) => {
                            this.preferredColorScheme = e.matches
                                ? "dark"
                                : "light";
                            // Update theme if set to system
                            if (this.$store.theme.get() === "system") {
                                e.matches
                                    ? this.setDarkTheme()
                                    : this.setLightTheme();
                            }
                        });

                    // Initialize theme on load
                    const currentTheme = this.$store.theme.get();
                    if (currentTheme === "system") {
                        window.matchMedia("(prefers-color-scheme: dark)")
                            .matches
                            ? this.setDarkTheme()
                            : this.setLightTheme();
                    } else if (currentTheme === "dark") {
                        this.setDarkTheme();
                    } else {
                        this.setLightTheme();
                    }
                },
                ["@keyup.escape.stop.prevent"]() {
                    this.themeToggleOpen = false;
                },
                ["@focusin.window"]() {
                    // Only close dropdown on desktop
                    if (window.innerWidth >= 640) {
                        !this.$refs.panel?.contains(this.$event.target) &&
                            (this.themeToggleOpen = false);
                    }
                },
            },
            toggle: {
                ["@click.prevent"]() {
                    this.themeToggleOpen = !this.themeToggleOpen;
                },
                [":aria-label"]() {
                    return this.themeToggleOpen
                        ? "Cerrar selector de tema"
                        : "Abrir selector de tema";
                },
                [":aria-expanded"]() {
                    return this.themeToggleOpen;
                },
                [":aria-controls"]() {
                    return this.$id("dropdown-button");
                },
            },
            panel: {
                ["x-show"]() {
                    return this.themeToggleOpen;
                },
                ["@click.outside"]() {
                    this.themeToggleOpen = false;
                },
                [":id"]() {
                    return this.$id("dropdown-button");
                },
                ["x-transition.opacity.duration.150ms"]() {},
            },
        };
    });

    // Inicializa el store
    Alpine.store("colorScheme").init();
});

// Call Alpine.
window.Alpine = Alpine;
Alpine.plugin([collapse, focus, morph, persist, precognition]);
Alpine.start();
