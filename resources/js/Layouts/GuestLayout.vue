<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4 dark:bg-gray-900"
    >
        <!-- Optional: Add your logo back if needed -->
        <!--
        <div class="mb-8">
            <Link href="/">
                <ApplicationLogo class="h-16 w-16 fill-current text-gray-800 dark:text-gray-200" />
            </Link>
        </div>
        -->

        <div
            class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-2xl shadow-blue-500/10 dark:shadow-gray-900/50 border border-white/20 dark:border-gray-700/30 overflow-hidden transition-all duration-300"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
/* Ensure the layout takes full viewport height without scrolling */
.min-h-screen {
    min-height: 100vh;
    height: 100vh;
    overflow: hidden;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>