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
    faRightToBracket,
    faBars,
    faTimes
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
    isEditMode: Boolean,
    publishedPages: {
        type: Array,
        default: ()=>['home', 'about', 'gallery', 'contact']
    }, 
});

// Mobile menu state
const mobileMenuOpen = ref(false);

// Enhanced page visibility check
const isPagePublished = (pageName) => {
    // Owner can see all pages in navigation
    if (props.isOwner) return true;
    
    // Check if page is in published pages list
    // Ensure publishedPages is always treated as an array
    const publishedPages = Array.isArray(props.publishedPages) ? props.publishedPages : [];
    return publishedPages.includes(pageName);
};

// Get available navigation pages
const availablePages = computed(() => {
    return ['home', 'about', 'gallery', 'contact'].filter(page => isPagePublished(page));
});

// Scroll detection for header
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

// Close mobile menu when clicking outside
const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// Handle escape key
const handleEscape = (event) => {
    if (event.key === 'Escape') {
        closeMobileMenu();
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('keydown', handleEscape);
});

// FIXED: Enhanced global styles with proper variable inheritance
const globalStyles = computed(() => {
    const customColors = props.websiteSettings?.customColors;
    
    // Get colors from custom colors or use defaults
    const primary = customColors?.primary || '#000000';
    const secondary = customColors?.secondary || '#8B4513';
    const accent = customColors?.accent || '#FFFFFF';
    
    // Calculate text colors based on background for better contrast
    const getContrastColor = (backgroundColor) => {
        const hex = backgroundColor.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminance > 0.5 ? '#000000' : '#FFFFFF';
    };

    // FIXED: Use simpler, more reliable variable structure
    return {
        '--color-primary': primary,
        '--color-secondary': secondary,
        '--color-accent': accent,
        
        // Use the actual colors for text and backgrounds
        '--text-primary': primary,
        '--text-secondary': secondary,
        '--text-accent': getContrastColor(accent),
        
        '--bg-primary': accent,
        '--bg-secondary': secondary,
        '--bg-accent': primary,
    };
});

// Replace rootStyles
const rootStyles = computed(() => globalStyles.value);

// Helper function to check if a color is light
const isLightColor = (color) => {
    if (!color) return true;
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

    <div class="min-h-screen bg-white global-theme" :style="rootStyles">
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
                        <Link :href="route('website.home')" @click="closeMobileMenu">
                            <h1 
                                class="text-xl sm:text-2xl font-bold cursor-pointer hover:opacity-80 transition-opacity"
                                :style="{ color: `var(--header-text-color, var(--color-primary))` }"
                            >
                                {{ websiteSettings?.siteName || 'Adventure Log' }}
                            </h1>
                        </Link>
                    </div>

                    <!-- Desktop Navigation Menu -->
                    <nav class="hidden md:flex space-x-4 lg:space-x-8">
                        <Link
                            v-for="navPage in availablePages"
                            :key="navPage"
                            :href="getPageRoute(navPage)"
                            class="px-2 lg:px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 transform hover:scale-105"
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

                    <!-- Desktop Auth Links -->
                    <div v-if="user" class="hidden md:flex items-center space-x-3 lg:space-x-4">
                        <Link
                            :href="route('dashboard')"
                            class="px-3 lg:px-4 py-2 rounded-lg hover:opacity-90 transition-all duration-300 transform hover:scale-105 text-sm font-medium shadow-lg"
                            :style="{
                                backgroundColor: 'black',
                                color: `var(--color-accent, #FFFFFF)`
                            }"
                        >
                            Dashboard
                        </Link>
                    </div>
                    <div v-else class="hidden md:block">
                        <Link
                            :href="route('login')"
                            class="hover:opacity-80 text-sm font-medium transition-colors inline-flex items-center space-x-2"
                            :style="{ color: `var(--header-text-color, var(--color-primary))` }"
                        >
                            <FontAwesomeIcon :icon="faRightToBracket" />
                            <span>Login</span>
                        </Link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100/50 transition-colors"
                        :style="{ color: `var(--header-text-color, var(--color-primary))` }"
                    >
                        <FontAwesomeIcon 
                            :icon="mobileMenuOpen ? faTimes : faBars" 
                            class="w-5 h-5"
                        />
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div 
                v-if="mobileMenuOpen"
                class="md:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-md border-b shadow-lg"
                :style="{
                    backgroundColor: `var(--header-bg-color, #FFFFFF)`,
                    borderColor: `var(--header-border-color, rgba(0, 0, 0, 0.1))`
                }"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <!-- Mobile Navigation Links -->
                    <nav class="space-y-3 mb-6">
                        <Link
                            v-for="navPage in availablePages"
                            :key="navPage"
                            :href="getPageRoute(navPage)"
                            @click="closeMobileMenu"
                            class="block px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 border-l-4"
                            :class="[
                                page === navPage
                                    ? 'bg-gray-100/50 font-semibold'
                                    : 'hover:bg-gray-100/30'
                            ]"
                            :style="{
                                color: `var(--header-text-color, var(--color-primary))`,
                                borderLeftColor: page === navPage ? `var(--header-text-color, var(--color-primary))` : 'transparent'
                            }"
                        >
                            {{ navPage.charAt(0).toUpperCase() + navPage.slice(1) }}
                        </Link>
                    </nav>

                    <!-- Mobile Auth Links -->
                    <div class="pt-4 border-t border-gray-200/50">
                        <div v-if="user" class="space-y-3">
                            <Link
                                :href="route('dashboard')"
                                @click="closeMobileMenu"
                                class="block w-full text-center px-4 py-3 rounded-xl font-medium shadow-lg transition-all duration-300"
                                :style="{
                                    backgroundColor: 'black',
                                    color: `var(--color-accent, #FFFFFF)`
                                }"
                            >
                                Dashboard
                            </Link>
                        </div>
                        <div v-else>
                            <Link
                                :href="route('login')"
                                @click="closeMobileMenu"
                                class="block w-full text-center px-4 py-3 rounded-xl font-medium border-2 transition-all duration-300"
                                :style="{
                                    borderColor:'black',
                                    color: `var(--color-primary, #000000)`
                                }"
                            >
                                <FontAwesomeIcon :icon="faRightToBracket" class="mr-2" />
                                Login
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu Overlay -->
        <div 
            v-if="mobileMenuOpen"
            class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 md:hidden"
            @click="closeMobileMenu"
        ></div>

       <!-- Main Content -->
        <main class="flex-grow pt-16 theme-content">
            <component 
                :is="CurrentPageComponent" 
                :page-content="pageContent"
                :user="user"
                :is-edit-mode="isEditMode"
                :published-pages="publishedPages"
                :website-settings="websiteSettings"
            />
        </main>

        <!-- Footer - Updated to use availablePages -->
        <footer class="bg-black text-white py-12 lg:py-16 mt-20 relative overflow-hidden theme-footer">
            <!-- Background decoration -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-10 left-10 w-20 h-20 sm:w-32 sm:h-32 bg-accent rounded-full blur-xl"></div>
                <div class="absolute bottom-10 right-10 w-24 h-24 sm:w-40 sm:h-40 bg-primary rounded-full blur-xl"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 sm:w-24 sm:h-24 bg-secondary rounded-full blur-lg"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Main Footer Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 mb-8 lg:mb-12">
                    <!-- Brand Section -->
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                            <div 
                                class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl flex items-center justify-center text-white text-xl lg:text-2xl shadow-lg"
                                :style="{
                                    background: `linear-gradient(135deg, var(--color-primary), var(--color-secondary))`
                                }"
                            >
                                <FontAwesomeIcon :icon="faCompass" />
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-black bg-gradient-to-r from-accent via-yellow-200 to-accent bg-clip-text text-transparent">
                                Adventure Log
                            </h3>
                        </div>
                        <p class="text-gray-300 text-base lg:text-lg leading-relaxed mb-4 lg:mb-6 max-w-md">
                            Where every journey becomes a story worth telling. Document your adventures, inspire fellow explorers, and create memories that last forever.
                        </p>
                        
                        <!-- Social Links -->
                        <div class="flex space-x-3 lg:space-x-4">
                            <a 
                                v-for="(social, index) in [
                                    { icon: faFacebook, color: 'hover:bg-primary' },
                                    { icon: faTwitter, color: 'hover:bg-blue-400' },
                                    { icon: faInstagram, color: 'hover:bg-pink-500' },
                                    { icon: faYoutube, color: 'hover:bg-red-500' }
                                ]" 
                                :key="index"
                                href="#" 
                                class="group w-10 h-10 lg:w-12 lg:h-12 bg-gray-800 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                :class="social.color"
                            >
                                <FontAwesomeIcon :icon="social.icon" class="text-gray-400 group-hover:text-white text-lg lg:text-xl" />
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links - Updated to use availablePages -->
                    <div>
                        <h4 class="text-lg lg:text-xl font-bold mb-4 lg:mb-6 text-accent inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faRocket" />
                            <span>Explore</span>
                        </h4>
                        <div class="space-y-2 lg:space-y-3">
                            <Link 
                                v-for="navPage in availablePages"
                                :key="navPage"
                                :href="getPageRoute(navPage)"
                                class="group flex items-center space-x-2 text-gray-300 hover:text-accent transition-all duration-300 hover:translate-x-2 text-sm lg:text-base"
                            >
                                <span class="w-2 h-2 bg-accent rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <span class="capitalize font-medium">{{ navPage }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- CTA Section -->
                    <div>
                        <h4 class="text-lg lg:text-xl font-bold mb-4 lg:mb-6 text-accent inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faStar" />
                            <span>Start Exploring</span>
                        </h4>
                        <p class="text-gray-300 mb-4 lg:mb-6 leading-relaxed text-sm lg:text-base">
                            Ready to document your incredible adventures and share them with the world?
                        </p>
                        
                        <div class="space-y-3 lg:space-y-4">
                            <Link 
                                v-if="!user"
                                :href="route('register')" 
                                class="group w-full text-white px-4 lg:px-6 py-3 lg:py-4 rounded-xl font-bold hover:shadow-2xl hover:scale-105 transition-all duration-300 inline-flex items-center justify-center space-x-2 text-sm lg:text-base"
                                :style="{
                                    background: `linear-gradient(135deg, var(--color-primary), var(--color-secondary))`
                                }"
                            >
                                <FontAwesomeIcon :icon="faRocket" class="text-lg group-hover:translate-x-1 transition-transform" />
                                <span>Start Free Journey</span>
                            </Link>
                            
                            <div class="text-center">
                                <p class="text-gray-400 text-xs lg:text-sm">
                                    Join <span class="text-accent font-bold">10,000+</span> adventurers
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 lg:gap-6 mb-8 lg:mb-12">
                    <div 
                        v-for="(feature, index) in [
                            { icon: faGlobeAmericas, text: 'Global Community' },
                            { icon: faMobileAlt, text: 'Mobile Friendly' },
                            { icon: faShieldAlt, text: 'Secure Platform' },
                            { icon: faHeart, text: 'Built with Passion' }
                        ]"
                        :key="index"
                        class="text-center p-3 lg:p-4 bg-gray-900/50 rounded-xl backdrop-blur-sm border border-gray-800 hover:border-primary transition-all duration-300"
                    >
                        <FontAwesomeIcon :icon="feature.icon" class="text-xl lg:text-2xl mb-2 text-accent" />
                        <div class="text-xs lg:text-sm text-gray-300">{{ feature.text }}</div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="border-t border-gray-800 pt-6 lg:pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-gray-400 text-xs lg:text-sm flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faCopyright" class="text-xs" />
                            <span>2024 Adventure Log. All adventures documented.</span>
                        </div>
                        
                        <div class="flex space-x-4 lg:space-x-6 text-xs lg:text-sm">
                            <a 
                                v-for="(link, index) in [
                                    { icon: faFileAlt, text: 'Privacy' },
                                    { icon: faGavel, text: 'Terms' },
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
                    
                    <div class="text-center mt-4 lg:mt-6">
                        <p class="text-gray-500 text-xs lg:text-sm flex items-center justify-center space-x-2">
                            <span>Made with</span>
                            <FontAwesomeIcon :icon="faHeart" class="text-red-500 text-xs" />
                            <span>for explorers worldwide</span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Edit Mode Indicator -->
        <div v-if="isEditMode && user" class="fixed bottom-4 right-4 bg-blue-600 text-white px-3 py-2 lg:px-4 lg:py-3 rounded-lg shadow-xl z-50 animate-pulse">
            <div class="flex items-center space-x-2 text-sm lg:text-base">
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
/* Global theme classes that use CSS variables */
.global-theme {
    color: var(--text-primary);
    background-color: var(--bg-primary);
}

.theme-header {
    background-color: var(--header-bg-color);
    color: var(--header-text-color);
    border-bottom-color: var(--header-border-color);
}

.theme-content {
    color: var(--text-primary);
    background-color: var(--bg-primary);
}

.theme-footer {
    background-color: var(--bg-accent);
    color: var(--text-accent);
}

/* Global color utility classes that work in child components */
:deep(.text-primary) {
    color: var(--color-primary) !important;
}

:deep(.bg-primary) {
    background-color: var(--color-primary) !important;
}

:deep(.border-primary) {
    border-color: var(--color-primary) !important;
}

:deep(.text-secondary) {
    color: var(--color-secondary) !important;
}

:deep(.bg-secondary) {
    background-color: var(--color-secondary) !important;
}

:deep(.border-secondary) {
    border-color: var(--color-secondary) !important;
}

:deep(.text-accent) {
    color: var(--color-accent) !important;
}

:deep(.bg-accent) {
    background-color: var(--color-accent) !important;
}

:deep(.border-accent) {
    border-color: var(--color-accent) !important;
}

/* Gradient classes */
:deep(.gradient-primary) {
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)) !important;
}

:deep(.gradient-secondary) {
    background: linear-gradient(135deg, var(--color-secondary), var(--color-primary)) !important;
}

/* Ensure these work with hover states */
:deep(.hover\\:text-primary:hover) {
    color: var(--color-primary) !important;
}

:deep(.hover\\:bg-primary:hover) {
    background-color: var(--color-primary) !important;
}

:deep(.hover\\:border-primary:hover) {
    border-color: var(--color-primary) !important;
}
/* Global theme classes that will use CSS variables */
.global-theme {
    color: var(--text-primary);
    background-color: var(--bg-primary);
}

.theme-header {
    background-color: var(--header-bg-color);
    color: var(--header-text-color);
    border-bottom-color: var(--header-border-color);
}

.theme-content {
    color: var(--text-primary);
    background-color: var(--bg-primary);
}

/* Update existing CSS variable classes to be more global */
.text-primary { color: var(--color-primary) !important; }
.bg-primary { background-color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }

.text-secondary { color: var(--color-secondary) !important; }
.bg-secondary { background-color: var(--color-secondary) !important; }
.border-secondary { border-color: var(--color-secondary) !important; }

.text-accent { color: var(--color-accent) !important; }
.bg-accent { background-color: var(--color-accent) !important; }
.border-accent { border-color: var(--color-accent) !important; }

/* Ensure these classes work in child components */
:deep(.text-primary) { color: var(--color-primary) !important; }
:deep(.bg-primary) { background-color: var(--color-primary) !important; }
:deep(.border-primary) { border-color: var(--color-primary) !important; }

:deep(.text-secondary) { color: var(--color-secondary) !important; }
:deep(.bg-secondary) { background-color: var(--color-secondary) !important; }
:deep(.border-secondary) { border-color: var(--color-secondary) !important; }

:deep(.text-accent) { color: var(--color-accent) !important; }
:deep(.bg-accent) { background-color: var(--color-accent) !important; }
:deep(.border-accent) { border-color: var(--color-accent) !important; }


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

/* Mobile menu animations */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
    transition: all 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Prevent body scroll when mobile menu is open */
body:has(.mobile-menu-open) {
    overflow: hidden;
}

/* Touch-friendly hover states */
@media (hover: none) and (pointer: coarse) {
    .hover\\:scale-105:hover {
        transform: scale(1);
    }
    
    .hover\\:translate-x-2:hover {
        transform: translateX(0);
    }
}
</style>