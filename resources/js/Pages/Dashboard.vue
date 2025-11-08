<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faCompass,
    faEye,
    faFileAlt,
    faChartBar,
    faCheckCircle,
    faEdit,
    faExternalLinkAlt,
    faTimes,
    faToggleOn,
    faToggleOff,
    faPalette,
    faSave,
    faHome,
    faUser,
    faImages,
    faEnvelope,
    faSignOutAlt,
    faDownload,
    faUsers,
    faChartLine,
    faArrowUp,
    faSpinner,
    faBars,
  
} from '@fortawesome/free-solid-svg-icons';
import LogoutDialog from './Auth/Logout.vue';

// Props
const props = defineProps({
    user: Object,
    websiteSettings: Object,
    analytics: Object,
    colorPalettes: Object,
    pageStatus: Object
});

const activeTab = ref('overview');
const analyticsData = ref(null);
const analyticsPeriod = ref('7days');
const loadingAnalytics = ref(false);
const exportLoading = ref(false);
const overviewLoading = ref(true); 
const showLogoutDialog = ref(false);
const mobileMenuOpen = ref(false);

// Logout dialog methods
const openLogoutDialog = () => {
    mobileMenuOpen.value = false; // Close mobile menu first
    // Small delay to ensure mobile menu is closed before showing dialog
    setTimeout(() => {
        showLogoutDialog.value = true;
    }, 50);
};

const closeLogoutDialog = () => {
    showLogoutDialog.value = false;
};

const colorForm = ref({
    colorPalette: props.websiteSettings?.colorPalette || 'default',
    customColors: props.websiteSettings?.customColors || {}
});

const PERIOD_OPTIONS = [
    { value: 'today', label: 'Today' },
    { value: '7days', label: 'Last 7 Days' },
    { value: '30days', label: 'Last 30 Days' },
    { value: '90days', label: 'Last 90 Days' }
];

const TAB_CONFIG = {
    overview: { icon: faChartBar, label: 'Overview' },
    pages: { icon: faFileAlt, label: 'Pages' },
    customization: { icon: faPalette, label: 'Customization' },
    analytics: { icon: faChartLine, label: 'Analytics' }
};

const PAGE_ICONS = {
    home: faHome,
    about: faUser,
    gallery: faImages,
    contact: faEnvelope
};

const fetchAnalytics = async (period = '7days') => {
    loadingAnalytics.value = true;
    overviewLoading.value = true;
    try {
        console.log(`🔄 Fetching analytics for period: ${period}`);
        
        const response = await fetch(`/dashboard/analytics?period=${period}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        });
        
        console.log('📡 Response status:', response.status, response.statusText);
        
        if (!response.ok) {
            let errorMessage = `HTTP Error: ${response.status} ${response.statusText}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.error || errorData.message || errorMessage;
            } catch (e) {
                const text = await response.text();
                errorMessage = text || errorMessage;
            }
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        console.log('📊 Analytics API response:', data);
        
        if (data.success) {
            analyticsData.value = data.data;
            console.log('✅ Analytics data loaded successfully');
        } else {
            throw new Error(data.error || 'API returned unsuccessful response');
        }
    } catch (error) {
        console.error('❌ Detailed error fetching analytics:', {
            message: error.message,
            stack: error.stack
        });
        
        analyticsData.value = null;
    } finally {
        loadingAnalytics.value = false;
        overviewLoading.value = false;
    }
};

const exportAnalytics = async (period = '30days') => {
    exportLoading.value = true;
    try {
        const response = await fetch(`/dashboard/analytics/export?period=${period}`);
        if (!response.ok) throw new Error('Failed to export analytics');
        
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `analytics-${period}-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        console.error('Error exporting analytics:', error);
    } finally {
        exportLoading.value = false;
    }
};

const updateColors = async () => {
    try {
        await router.post('/dashboard/settings', colorForm.value);
    } catch (error) {
        console.error('Error updating colors:', error);
    }
};

const selectPalette = (paletteKey) => {
    colorForm.value.colorPalette = paletteKey;
    colorForm.value.customColors = { 
        ...props.colorPalettes[paletteKey]?.colors || {} 
    };
};

const togglePublish = async (page, currentStatus) => {
    try {
        await router.post(`/dashboard/pages/${page}/publish`, {
            published: !currentStatus
        });
    } catch (error) {
        console.error('Error toggling publish:', error);
    }
};

// Computed properties
const publishedPagesCount = () => {
    return Object.values(props.pageStatus || {}).filter(page => page.published).length;
};

const totalPagesCount = () => {
    return Object.keys(props.pageStatus || {}).length;
};

// Lifecycle
onMounted(() => {
    fetchAnalytics(analyticsPeriod.value);
});
</script>

<template>
    <Head title="Dashboard - Adventure Log" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Navigation -->
     <!-- Navigation -->
<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                    <FontAwesomeIcon :icon="faHome" class="text-white text-sm" />
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                    Dashboard
                </h1>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-2">
                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                    >
                        <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center">
                            <FontAwesomeIcon :icon="faUser" class="text-white text-sm" />
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 max-w-32 truncate">
                            {{ user?.name || user?.email || 'User' }}
                        </span>
                        <svg 
                            class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                            :class="{ 'rotate-180': open }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        v-if="open"
                        class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl z-50 animate-fade-in"
                        @click.outside="open = false"
                    >
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center flex-shrink-0">
                                    <FontAwesomeIcon :icon="faUser" class="text-white text-base" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ user?.name || 'User' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ user?.email || 'No email' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Member since</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ user?.createdAt ? new Date(user.createdAt).toLocaleDateString() : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Status</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                    Active
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="p-3 border-t border-gray-100 dark:border-gray-700 space-y-1">
                            <Link 
                                :href="route('profile.edit')"
                                class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                <FontAwesomeIcon :icon="faEdit" class="text-gray-400 text-sm" />
                                <span>Edit Profile</span>
                            </Link>
                            <Link 
                                :href="route('website.home')" 
                                target="_blank"
                                class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-gray-400 text-sm" />
                                <span>View Live Site</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Logout Button -->
                <button 
                    @click="openLogoutDialog"
                    class="group flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm border border-gray-200 dark:border-gray-600"
                >
                    <FontAwesomeIcon :icon="faSignOutAlt" class="text-sm group-hover:scale-110 transition-transform" />
                    <span>Logout</span>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
            >
                <FontAwesomeIcon 
                    :icon="mobileMenuOpen ? faTimes : faBars" 
                    class="w-5 h-5 text-gray-600 dark:text-gray-300"
                />
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div 
        v-if="mobileMenuOpen && !showLogoutDialog"
        class="md:hidden absolute top-16 left-0 w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-lg z-50"
    >
        <div class="px-4 py-3 space-y-3">
            <!-- Mobile User Info -->
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center flex-shrink-0">
                    <FontAwesomeIcon :icon="faUser" class="text-white text-sm" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ user?.name || 'User' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ user?.email || 'No email' }}
                    </p>
                </div>
            </div>

            <Link 
                :href="route('website.home')" 
                @click="mobileMenuOpen = false"
                class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
                target="_blank"
            >
                <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-sm" />
                <span>View Live Site</span>
            </Link>
            <Link 
                :href="route('profile.edit')"
                @click="mobileMenuOpen = false"
                class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
            >
                <FontAwesomeIcon :icon="faEdit" class="text-sm" />
                <span>Edit Profile</span>
            </Link>
            <button 
                @click="openLogoutDialog"
                class="w-full flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-left"
            >
                <FontAwesomeIcon :icon="faSignOutAlt" class="text-sm" />
                <span>Logout</span>
            </button>
        </div>
    </div>
</nav>

        <!-- Mobile Menu Overlay -->
        <div 
            v-if="mobileMenuOpen"
            class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 md:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
                <nav class="-mb-px flex space-x-2 sm:space-x-8 min-w-max">
                    <button
                        v-for="(tabConfig, tabKey) in TAB_CONFIG"
                        :key="tabKey"
                        @click="activeTab = tabKey"
                        :class="[
                            'group whitespace-nowrap py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-xs sm:text-sm transition-all duration-300 flex items-center space-x-1 sm:space-x-2',
                            activeTab === tabKey
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                        ]"
                    >
                        <FontAwesomeIcon 
                            :icon="tabConfig.icon" 
                            class="text-base sm:text-lg group-hover:scale-110 transition-transform"
                        />
                        <span class="hidden xs:inline">{{ tabConfig.label }}</span>
                    </button>
                </nav>
            </div>

            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="space-y-4 sm:space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                    <!-- Total Visitors Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Total Visitors
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                            {{ analyticsData?.summary?.unique_visitors || 0 }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-green-600 dark:text-green-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faArrowUp" class="text-xs mr-1" />
                                            <span>All Time</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faUsers" class="text-white text-sm sm:text-base lg:text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Views Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Page Views
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                            {{ analyticsData?.summary?.total_views || 0 }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faChartLine" class="text-xs mr-1" />
                                            <span>All Time</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faEye" class="text-white text-sm sm:text-base lg:text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Types Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Visitor Types
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-1">
                                        <div class="space-y-1">
                                            <div class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                            <div class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else class="mt-1 sm:mt-2 space-y-1">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                            New: {{ analyticsData?.visitor_stats?.new_visitors || 0 }}
                                        </p>
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                            Returning: {{ analyticsData?.visitor_stats?.returning_visitors || 0 }}
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faUsers" class="text-white text-sm sm:text-base lg:text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Published Pages Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Published Pages
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                            {{ publishedPagesCount() }}/{{ totalPagesCount() }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-orange-600 dark:text-orange-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-xs mr-1" />
                                            <span>Live</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faCheckCircle" class="text-white text-sm sm:text-base lg:text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center">
                            <FontAwesomeIcon :icon="faCompass" class="text-white text-sm sm:text-base lg:text-lg" />
                        </div>
                        <span>Quick Actions</span>
                    </h3>
                    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <Link 
                            v-for="page in Object.keys(PAGE_ICONS)"
                            :key="page"
                            :href="route('website.editor', { page })"
                            class="group/card p-3 sm:p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg sm:rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 hover:scale-105"
                        >
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover/card:scale-105 sm:group-hover/card:scale-110 transition-transform duration-300">
                                    <FontAwesomeIcon 
                                        :icon="PAGE_ICONS[page]" 
                                        class="text-white text-sm sm:text-base" 
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm capitalize truncate">Edit {{ page }} Page</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1 mt-1">
                                        <FontAwesomeIcon 
                                            :icon="faCheckCircle" 
                                            class="text-green-500 text-xs" 
                                            v-if="pageStatus[page]?.published" 
                                        />
                                        <span class="truncate">{{ pageStatus[page]?.published ? 'Published' : 'Draft' }}</span>
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div v-if="activeTab === 'analytics'" class="space-y-4 sm:space-y-6">
                <!-- Analytics Header -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2 sm:space-x-3">
                                <FontAwesomeIcon :icon="faChartLine" class="text-green-500 text-lg sm:text-xl lg:text-2xl" />
                                <span class="truncate">Website Analytics</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1 text-xs sm:text-sm">
                                Track your website performance and visitor behavior
                            </p>
                        </div>
                        <div class="flex flex-col xs:flex-row items-stretch xs:items-center space-y-2 xs:space-y-0 xs:space-x-3 sm:space-x-4">
                            <select 
                                v-model="analyticsPeriod"
                                @change="fetchAnalytics(analyticsPeriod)"
                                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm"
                            >
                                <option 
                                    v-for="option in PERIOD_OPTIONS" 
                                    :key="option.value" 
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <button
                                @click="exportAnalytics(analyticsPeriod)"
                                :disabled="exportLoading"
                                class="group/btn bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-2 disabled:opacity-50 text-xs sm:text-sm"
                            >
                                <FontAwesomeIcon 
                                    :icon="exportLoading ? faSpinner : faDownload" 
                                    class="text-xs sm:text-sm" 
                                    :class="{ 'animate-spin': exportLoading }" 
                                />
                                <span>Export CSV</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loadingAnalytics" class="text-center py-8 sm:py-12">
                    <FontAwesomeIcon :icon="faSpinner" class="text-3xl sm:text-4xl text-blue-500 animate-spin" />
                    <p class="text-gray-600 dark:text-gray-400 mt-3 sm:mt-4 text-sm sm:text-base">Loading analytics data...</p>
                </div>

                <!-- Analytics Content -->
                <div v-else-if="analyticsData" class="space-y-4 sm:space-y-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                        {{ analyticsData.summary.total_views }}
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faEye" class="text-blue-500 text-lg sm:text-xl lg:text-2xl" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Unique Visitors</p>
                                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                        {{ analyticsData.summary.unique_visitors }}
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faUsers" class="text-green-500 text-lg sm:text-xl lg:text-2xl" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Returning Rate</p>
                                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                                        {{ analyticsData.visitor_stats.total_visitors > 0 ? 
                                            Math.round((analyticsData.visitor_stats.returning_visitors / analyticsData.visitor_stats.total_visitors) * 100) : 0 }}%
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faChartLine" class="text-purple-500 text-lg sm:text-xl lg:text-2xl" />
                            </div>
                        </div>
                    </div>

                    <!-- Page Performance -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Page Performance</h4>
                        <div class="space-y-3 sm:space-y-4">
                            <div 
                                v-for="page in analyticsData.page_stats" 
                                :key="page.page"
                                class="flex items-center justify-between p-3 sm:p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                            >
                                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                    <FontAwesomeIcon 
                                        :icon="PAGE_ICONS[page.page] || faFileAlt" 
                                        class="text-blue-500 text-base sm:text-lg flex-shrink-0" 
                                    />
                                    <span class="font-medium text-gray-900 dark:text-white capitalize truncate text-xs sm:text-sm">
                                        {{ page.page }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-3 sm:space-x-4 ml-2">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                                        <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">{{ page.views }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Unique</p>
                                        <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">{{ page.unique_views }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Analytics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Visitor Types -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Visitor Types</h4>
                            <div class="space-y-3 sm:space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">New Visitors</span>
                                    <span class="font-bold text-green-600 dark:text-green-400 text-sm">
                                        {{ analyticsData.visitor_stats.new_visitors }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Returning Visitors</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">
                                        {{ analyticsData.visitor_stats.returning_visitors }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-semibold text-sm">Total Visitors</span>
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">
                                        {{ analyticsData.visitor_stats.total_visitors }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Time Series Chart -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Views Over Time</h4>
                            <div class="h-32 sm:h-40 lg:h-48 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="text-center">
                                    <FontAwesomeIcon :icon="faChartLine" class="text-2xl sm:text-3xl lg:text-4xl mb-2 opacity-50" />
                                    <p class="text-xs sm:text-sm">Chart visualization would be implemented here</p>
                                    <p class="text-xs mt-1 sm:mt-2">({{ analyticsData.time_series?.length || 0 }} data points)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Data State -->
                <div v-else class="text-center py-8 sm:py-12">
                    <FontAwesomeIcon :icon="faChartLine" class="text-3xl sm:text-4xl text-gray-400 mb-3 sm:mb-4" />
                    <h3 class="text-lg sm:text-xl font-bold text-gray-600 dark:text-gray-400 mb-2">No Analytics Data Yet</h3>
                    <p class="text-gray-500 dark:text-gray-500 text-sm sm:text-base">Your analytics data will appear here once you start receiving visitors.</p>
                </div>
            </div>

            <!-- Pages Tab -->
            <div v-if="activeTab === 'pages'" class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center space-x-2 sm:space-x-3">
                        <FontAwesomeIcon :icon="faFileAlt" class="text-blue-500 text-lg sm:text-xl lg:text-2xl" />
                        <span>Manage Pages</span>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div 
                        v-for="(status, page) in pageStatus" 
                        :key="page"
                        class="group/item px-4 sm:px-6 py-4 sm:py-5 flex flex-col xs:flex-row xs:items-center xs:justify-between space-y-3 xs:space-y-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-300"
                    >
                        <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover/item:scale-105 sm:group-hover/item:scale-110 transition-transform duration-300 flex-shrink-0">
                                <FontAwesomeIcon 
                                    :icon="PAGE_ICONS[page] || faFileAlt" 
                                    class="text-white text-sm sm:text-base" 
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base capitalize truncate">{{ page }} Page</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm truncate">{{ status.title }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            <Link 
                                :href="route('website.editor', { page })"
                                class="group/btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1 sm:space-x-2"
                            >
                                <FontAwesomeIcon :icon="faEdit" class="text-xs group-hover/btn:rotate-12 transition-transform" />
                                <span>Edit</span>
                            </Link>
                            <button
                                @click="togglePublish(page, status.published)"
                                :class="[
                                    'group/btn px-3 py-2 rounded-lg text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1 sm:space-x-2',
                                    status.published 
                                        ? 'bg-green-600 hover:bg-green-700 text-white' 
                                        : 'bg-gray-600 hover:bg-gray-700 text-white'
                                ]"
                            >
                                <FontAwesomeIcon :icon="status.published ? faToggleOn : faToggleOff" class="text-xs group-hover/btn:scale-110 transition-transform" />
                                <span>{{ status.published ? 'Published' : 'Set Live' }}</span>
                            </button>
                            <Link 
                                :href="route('website.page', { page })"
                                target="_blank"
                                class="group/btn text-blue-600 hover:text-blue-800 text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1"
                            >
                                <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-xs group-hover/btn:translate-x-1 transition-transform" />
                                <span>Preview</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customization Tab -->
            <div v-if="activeTab === 'customization'" class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center space-x-2 sm:space-x-3">
                    <FontAwesomeIcon :icon="faPalette" class="text-purple-500 text-lg sm:text-xl lg:text-2xl" />
                    <span>Customize Colors</span>
                </h3>
                
                <!-- Color Palettes -->
                <div class="mb-6 sm:mb-8">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 text-sm sm:text-base">Choose a Color Palette</h4>
                    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <div
                            v-for="(palette, key) in colorPalettes"
                            :key="key"
                            @click="selectPalette(key)"
                            :class="[
                                'group/palette border-2 rounded-lg sm:rounded-xl p-3 sm:p-4 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-lg',
                                colorForm.colorPalette === key 
                                    ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800 bg-blue-50 dark:bg-blue-900/20' 
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'
                            ]"
                        >
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3 text-xs sm:text-sm group-hover/palette:text-blue-600 dark:group-hover/palette:text-blue-400 transition-colors truncate">
                                {{ palette.name }}
                            </h5>
                            <div class="flex space-x-1 sm:space-x-2">
                                <div
                                    v-for="(color, colorKey) in palette.colors"
                                    :key="colorKey"
                                    class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 rounded-lg border border-gray-200 dark:border-gray-700 group-hover/palette:scale-105 sm:group-hover/palette:scale-110 transition-transform duration-300"
                                    :style="{ backgroundColor: color }"
                                    :title="`${colorKey}: ${color}`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Color Editor -->
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 text-sm sm:text-base">Custom Colors</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                        <div 
                            v-for="(color, key) in colorForm.customColors" 
                            :key="key" 
                            class="group/color"
                        >
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2 capitalize">
                                {{ key }} Color
                            </label>
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <input
                                    type="color"
                                    v-model="colorForm.customColors[key]"
                                    class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer group-hover/color:scale-105 sm:group-hover/color:scale-110 transition-transform duration-300"
                                />
                                <input
                                    type="text"
                                    v-model="colorForm.customColors[key]"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                    placeholder="#000000"
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        @click="updateColors"
                        class="group/btn bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl transition-all duration-300 hover:scale-105 font-semibold flex items-center justify-center space-x-1 sm:space-x-2 shadow-lg hover:shadow-xl text-sm sm:text-base w-full sm:w-auto"
                    >
                        <FontAwesomeIcon :icon="faSave" class="group-hover/btn:rotate-12 transition-transform" />
                        <span>Save Color Changes</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Logout Dialog -->
        <LogoutDialog 
            :show-dialog="showLogoutDialog" 
            @close="closeLogoutDialog" 
        />
    </div>
</template>

<style scoped>
/* Custom breakpoint for extra small screens */
@media (min-width: 475px) {
    .xs\:inline {
        display: inline !important;
    }
    .xs\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .xs\:flex-row {
        flex-direction: row;
    }
    .xs\:items-center {
        align-items: center;
    }
    .xs\:space-x-3 {
        gap: 0.75rem;
    }
    .xs\:space-y-0 > * + * {
        margin-top: 0px;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .hover\:scale-105:hover,
    .hover\:-translate-y-1:hover,
    .hover\:-translate-y-2:hover {
        transform: none;
    }
    
    .group-hover\/item:scale-110:hover {
        transform: none;
    }
}

/* Improved focus styles for accessibility */
button:focus-visible,
select:focus-visible,
input:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Smooth scrolling for better mobile experience */
html {
    scroll-behavior: smooth;
}

/* Prevent layout shift on mobile */
img, svg {
    max-width: 100%;
    height: auto;
}
</style>