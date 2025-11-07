<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
    faCompass,
    faRocket,
    faStar,
    faGlobeAmericas,
    faMobileAlt,
    faShieldAlt,
    faHeart,
    faCopyright,
    faFileAlt,
    faGavel,
    faEnvelope,
    faRightToBracket
} from '@fortawesome/free-solid-svg-icons'
import { faFacebook, faTwitter, faInstagram, faYoutube } from '@fortawesome/free-brands-svg-icons'
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Import individual page components
import HomePage from './HomePage.vue';
import AboutPage from './AboutPage.vue';
import GalleryPage from './GalleryPage.vue';
import ContactPage from './ContactPage.vue';

const props = defineProps({
    user: Object,
    page: String,
    pageContent: Object,
    websiteSettings: Object,
    isEditMode: Boolean
});

// Scroll detection for header
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Compute CSS variables for inline styles with smart header colors
const rootStyles = computed(() => {
    const customColors = props.websiteSettings?.customColors;
    const palette = props.websiteSettings?.colorPalette || 'default';
    
    // Get header colors based on palette
    let headerTextColor = customColors?.primary || '#000000';
    let headerBgColor = customColors?.accent || '#FFFFFF';
    let headerBorderColor = 'rgba(0, 0, 0, 0.1)';

    // Adjust colors for scrolled state
    if (isScrolled.value) {
        // When scrolled, ensure good contrast
        headerBgColor = customColors?.accent || '#FFFFFF';
        headerTextColor = customColors?.primary || '#000000';
        headerBorderColor = 'rgba(0, 0, 0, 0.1)';
    }

    return {
        '--color-primary': customColors?.primary || '#000000',
        '--color-secondary': customColors?.secondary || '#8B4513',
        '--color-accent': customColors?.accent || '#FFFFFF',
        '--header-text-color': headerTextColor,
        '--header-bg-color': headerBgColor,
        '--header-border-color': headerBorderColor,
    };
});

// Helper function to check if a color is light
const isLightColor = (color) => {
    // Convert hex to RGB
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    
    // Calculate luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5;
};

// Compute header styles
const headerStyles = computed(() => {
    const customColors = props.websiteSettings?.customColors;
    const primaryColor = customColors?.primary || '#000000';
    const accentColor = customColors?.accent || '#FFFFFF';
    
    // Determine text color based on background
    const textColor = isLightColor(accentColor) ? primaryColor : accentColor;
    
    return {
        backgroundColor: `var(--header-bg-color, ${accentColor})`,
        color: `var(--header-text-color, ${textColor})`,
        borderBottomColor: `var(--header-border-color, rgba(0, 0, 0, 0.1))`,
    };
});

// Dynamic page components
const pageComponents = {
    home: HomePage,
    about: AboutPage,
    gallery: GalleryPage,
    contact: ContactPage
};

const CurrentPageComponent = computed(() => pageComponents[props.page] || HomePage);

// Helper function to get correct route for each page
const getPageRoute = (pageName) => {
    if (pageName === 'home') {
        return route('website.home');
    }
    return route('website.page', { page: pageName });
};
</script>

<template>
    <Head :title="pageContent?.title || 'Adventure Log'" />

    <div class="min-h-screen bg-white" :style="rootStyles">
        <!-- Enhanced Smart Header -->
        <header 
            class="fixed top-0 left-0 w-full backdrop-blur-md shadow-sm border-b z-50 transition-all duration-300"
            :class="{
                'bg-white/95': isScrolled,
                'bg-accent/90': !isScrolled
            }"
            :style="headerStyles"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('website.home')">
                            <h1 
                                class="text-2xl font-bold cursor-pointer hover:opacity-80 transition-opacity"
                                :style="{ color: `var(--header-text-color, var(--color-primary))` }"
                            >
                                {{ websiteSettings?.siteName || 'WowLogBook' }}
                            </h1>
                        </Link>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="hidden md:flex space-x-8">
                        <Link
                            v-for="navPage in ['home', 'about', 'gallery', 'contact']"
                            :key="navPage"
                            :href="getPageRoute(navPage)"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 transform hover:scale-105"
                            :class="[
                                page === navPage
                                    ? 'border-b-2 font-semibold'
                                    : 'hover:opacity-80'
                            ]"
                            :style="{
                                color: `var(--header-text-color, var(--color-primary))`,
                                borderColor: page === navPage ? `var(--header-text-color, var(--color-primary))` : 'transparent'
                            }"
                        >
                            {{ navPage.charAt(0).toUpperCase() + navPage.slice(1) }}
                        </Link>
                    </nav>

                    <!-- Dashboard Link / Login -->
                    <div v-if="user" class="flex items-center space-x-4">
                        <Link
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-lg hover:opacity-90 transition-all duration-300 transform hover:scale-105 text-sm font-medium shadow-lg"
                            :style="{
                                backgroundColor: `var(--color-primary, #000000)`,
                                color: `var(--color-accent, #FFFFFF)`
                            }"
                        >
                            Dashboard
                        </Link>
                    </div>
                    <div v-else>
                        <Link
                            :href="route('login')"
                            class="hover:opacity-80 text-sm font-medium transition-colors inline-flex items-center space-x-2"
                            :style="{ color: `var(--header-text-color, var(--color-primary))` }"
                        >
                            <FontAwesomeIcon :icon="faRightToBracket" />
                            <span>Login</span>
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow pt-16">
            <component 
                :is="CurrentPageComponent" 
                :page-content="pageContent"
                :user="user"
                :is-edit-mode="isEditMode"
            />
        </main>

        <!-- Footer (rest of your existing footer code remains the same) -->
        <footer class="bg-black text-white py-16 mt-20 relative overflow-hidden">
            <!-- Your existing footer content -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-10 left-10 w-32 h-32 bg-accent rounded-full blur-xl"></div>
                <div class="absolute bottom-10 right-10 w-40 h-40 bg-primary rounded-full blur-xl"></div>
                <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-secondary rounded-full blur-lg"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Main Footer Content -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 mb-12">
                    <!-- Brand Section -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center space-x-3 mb-6">
                            <div 
                                class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg"
                                :style="{
                                    background: `linear-gradient(135deg, var(--color-primary), var(--color-secondary))`
                                }"
                            >
                                <FontAwesomeIcon :icon="faCompass" />
                            </div>
                            <h3 class="text-3xl font-black bg-gradient-to-r from-accent via-yellow-200 to-accent bg-clip-text text-transparent">
                                Adventure Log
                            </h3>
                        </div>
                        <p class="text-gray-300 text-lg leading-relaxed mb-6 max-w-md">
                            Where every journey becomes a story worth telling. Document your adventures, inspire fellow explorers, and create memories that last forever.
                        </p>
                        
                        <!-- Social Links -->
                        <div class="flex space-x-4">
                            <a 
                                v-for="(social, index) in [
                                    { icon: faFacebook, color: 'hover:bg-primary' },
                                    { icon: faTwitter, color: 'hover:bg-blue-400' },
                                    { icon: faInstagram, color: 'hover:bg-pink-500' },
                                    { icon: faYoutube, color: 'hover:bg-red-500' }
                                ]" 
                                :key="index"
                                href="#" 
                                class="group w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                :class="social.color"
                            >
                                <FontAwesomeIcon :icon="social.icon" class="text-gray-400 group-hover:text-white text-xl" />
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-xl font-bold mb-6 text-accent inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faRocket" />
                            <span>Explore</span>
                        </h4>
                        <div class="space-y-3">
                            <Link 
                                v-for="navPage in ['home', 'about', 'gallery', 'contact']"
                                :key="navPage"
                                :href="getPageRoute(navPage)"
                                class="group flex items-center space-x-2 text-gray-300 hover:text-accent transition-all duration-300 hover:translate-x-2"
                            >
                                <span class="w-2 h-2 bg-accent rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <span class="capitalize font-medium">{{ navPage }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- CTA Section -->
                    <div>
                        <h4 class="text-xl font-bold mb-6 text-accent inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faStar" />
                            <span>Start Exploring</span>
                        </h4>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Ready to document your incredible adventures and share them with the world?
                        </p>
                        
                        <div class="space-y-4">
                            <Link 
                                v-if="!user"
                                :href="route('register')" 
                                class="group w-full text-white px-6 py-4 rounded-xl font-bold hover:shadow-2xl hover:scale-105 transition-all duration-300 inline-flex items-center justify-center space-x-2"
                                :style="{
                                    background: `linear-gradient(135deg, var(--color-primary), var(--color-secondary))`
                                }"
                            >
                                <FontAwesomeIcon :icon="faRocket" class="text-xl group-hover:translate-x-1 transition-transform" />
                                <span>Start Free Journey</span>
                            </Link>
                            
                            <div class="text-center">
                                <p class="text-gray-400 text-sm">
                                    Join <span class="text-accent font-bold">10,000+</span> adventurers
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                    <div 
                        v-for="(feature, index) in [
                            { icon: faGlobeAmericas, text: 'Global Community' },
                            { icon: faMobileAlt, text: 'Mobile Friendly' },
                            { icon: faShieldAlt, text: 'Secure Platform' },
                            { icon: faHeart, text: 'Built with Passion' }
                        ]"
                        :key="index"
                        class="text-center p-4 bg-gray-900/50 rounded-xl backdrop-blur-sm border border-gray-800 hover:border-primary transition-all duration-300"
                    >
                        <FontAwesomeIcon :icon="feature.icon" class="text-2xl mb-2 text-accent" />
                        <div class="text-sm text-gray-300">{{ feature.text }}</div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="border-t border-gray-800 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-gray-400 text-sm flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faCopyright" class="text-xs" />
                            <span>2024 Adventure Log. All adventures documented.</span>
                        </div>
                        
                        <div class="flex space-x-6 text-sm">
                            <a 
                                v-for="(link, index) in [
                                    { icon: faFileAlt, text: 'Privacy Policy' },
                                    { icon: faGavel, text: 'Terms of Service' },
                                    { icon: faEnvelope, text: 'Contact' }
                                ]"
                                :key="index"
                                href="#" 
                                class="text-gray-400 hover:text-accent transition-colors duration-300 flex items-center space-x-1"
                            >
                                <FontAwesomeIcon :icon="link.icon" class="text-xs" />
                                <span>{{ link.text }}</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="text-center mt-6">
                        <p class="text-gray-500 text-sm flex items-center justify-center space-x-2">
                            <span>Made with</span>
                            <FontAwesomeIcon :icon="faHeart" class="text-red-500 text-xs" />
                            <span>for explorers worldwide</span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Edit Mode Indicator -->
        <div v-if="isEditMode && user" class="fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-xl z-50 animate-pulse">
            <div class="flex items-center space-x-2">
                <span class="text-lg">✏️</span>
                <span class="font-medium">Edit Mode Active</span>
            </div>
        </div>

        <!-- Floating Action Button for Mobile -->
        <div class="fixed bottom-4 left-4 md:hidden z-40">
            <Link 
                :href="route('dashboard')" 
                v-if="user"
                class="p-3 rounded-full shadow-xl hover:scale-110 transition-all duration-300"
                :style="{
                    backgroundColor: `var(--color-primary, #000000)`,
                    color: `var(--color-accent, #FFFFFF)`
                }"
            >
                📊
            </Link>
        </div>
    </div>
</template>

<style scoped>
/* CSS Variables will be applied via inline styles */
.text-primary { color: var(--color-primary, #000000); }
.bg-primary { background-color: var(--color-primary, #000000); }
.border-primary { border-color: var(--color-primary, #000000); }

.text-secondary { color: var(--color-secondary, #8B4513); }
.bg-secondary { background-color: var(--color-secondary, #8B4513); }
.border-secondary { border-color: var(--color-secondary, #8B4513); }

.text-accent { color: var(--color-accent, #FFFFFF); }
.bg-accent { background-color: var(--color-accent, #FFFFFF); }
.border-accent { border-color: var(--color-accent, #FFFFFF); }

/* Header specific styles */
.header-text { color: var(--header-text-color, var(--color-primary)); }
.header-bg { background-color: var(--header-bg-color, var(--color-accent)); }
.header-border { border-color: var(--header-border-color, rgba(0, 0, 0, 0.1)); }

html {
    scroll-behavior: smooth;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Ensure header text is always visible */
header {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Smooth transitions for all interactive elements */
header * {
    transition: all 0.3s ease;
}
</style>