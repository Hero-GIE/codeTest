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
    faGlobe,
    faToggleOn,
    faToggleOff,
    faPalette,
    faSave,
    faHome,
    faUser,
    faImages,
    faEnvelope,
    faSignOutAlt,
    faExternalLinkAlt,
    faDownload,
    faCalendar,
    faUsers,
    faChartLine,
    faArrowUp,
    faArrowDown,
    faSpinner
} from '@fortawesome/free-solid-svg-icons';

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
const overviewLoading = ref(true); // Add loading state for overview

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

// Methods
const fetchAnalytics = async (period = '7days') => {
    loadingAnalytics.value = true;
    overviewLoading.value = true; // Set overview loading when fetching analytics
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
        overviewLoading.value = false; // Turn off overview loading when done
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
        router.reload();
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
        <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                            <FontAwesomeIcon :icon="faHome" class="text-white text-sm" />
                        </div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            Dashboard
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('website.home')" 
                            class="group flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            target="_blank"
                        >
                            <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-sm" />
                            <span>View Live Site</span>
                        </Link>
                        <Link 
                            :href="route('logout')" 
                            method="post"
                            class="group flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <FontAwesomeIcon :icon="faSignOutAlt" class="text-sm" />
                            <span>Logout</span>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                        v-for="(tabConfig, tabKey) in TAB_CONFIG"
                        :key="tabKey"
                        @click="activeTab = tabKey"
                        :class="[
                            'group whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 flex items-center space-x-2',
                            activeTab === tabKey
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                        ]"
                    >
                        <FontAwesomeIcon 
                            :icon="tabConfig.icon" 
                            class="text-lg group-hover:scale-110 transition-transform"
                        />
                        <span>{{ tabConfig.label }}</span>
                    </button>
                </nav>
            </div>

            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Total Visitors Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Total Visitors
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                            {{ analyticsData?.summary?.unique_visitors || 0 }}
                                        </p>
                                        <p class="text-sm text-green-600 dark:text-green-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faArrowUp" class="text-xs mr-1" />
                                            <span>All Time</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faUsers" class="text-white text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Views Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Page Views
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                            {{ analyticsData?.summary?.total_views || 0 }}
                                        </p>
                                        <p class="text-sm text-blue-600 dark:text-blue-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faChartLine" class="text-xs mr-1" />
                                            <span>All Time</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faEye" class="text-white text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Types Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Visitor Types
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="space-y-1">
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else class="mt-2 space-y-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            New: {{ analyticsData?.visitor_stats?.new_visitors || 0 }}
                                        </p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            Returning: {{ analyticsData?.visitor_stats?.returning_visitors || 0 }}
                                        </p>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faUsers" class="text-white text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Published Pages Card -->
                    <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Published Pages
                                    </p>
                                    
                                    <!-- Skeleton Loading -->
                                    <div v-if="overviewLoading" class="mt-2 space-y-2">
                                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20"></div>
                                    </div>
                                    
                                    <!-- Actual Content -->
                                    <div v-else>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                            {{ publishedPagesCount() }}/{{ totalPagesCount() }}
                                        </p>
                                        <p class="text-sm text-orange-600 dark:text-orange-400 flex items-center mt-1">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-xs mr-1" />
                                            <span>Live</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <FontAwesomeIcon :icon="faCheckCircle" class="text-white text-lg" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="group bg-white dark:bg-gray-800 shadow rounded-2xl p-6 hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <FontAwesomeIcon :icon="faCompass" class="text-white text-lg" />
                        </div>
                        <span>Quick Actions</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link 
                            v-for="page in Object.keys(PAGE_ICONS)"
                            :key="page"
                            :href="route('website.editor', { page })"
                            class="group/card p-5 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 hover:scale-105"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center group-hover/card:scale-110 transition-transform duration-300">
                                    <FontAwesomeIcon 
                                        :icon="PAGE_ICONS[page]" 
                                        class="text-white text-lg" 
                                    />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white capitalize">Edit {{ page }} Page</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-1 mt-1">
                                        <FontAwesomeIcon 
                                            :icon="faCheckCircle" 
                                            class="text-green-500 text-xs" 
                                            v-if="pageStatus[page]?.published" 
                                        />
                                        <span>{{ pageStatus[page]?.published ? 'Published' : 'Draft' }}</span>
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div v-if="activeTab === 'analytics'" class="space-y-6">
                <!-- Analytics Header -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-3">
                                <FontAwesomeIcon :icon="faChartLine" class="text-green-500 text-2xl" />
                                <span>Website Analytics</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Track your website performance and visitor behavior
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <select 
                                v-model="analyticsPeriod"
                                @change="fetchAnalytics(analyticsPeriod)"
                                class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                                class="group/btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 disabled:opacity-50"
                            >
                                <FontAwesomeIcon 
                                    :icon="exportLoading ? faSpinner : faDownload" 
                                    class="text-sm" 
                                    :class="{ 'animate-spin': exportLoading }" 
                                />
                                <span>Export CSV</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loadingAnalytics" class="text-center py-12">
                    <FontAwesomeIcon :icon="faSpinner" class="text-4xl text-blue-500 animate-spin" />
                    <p class="text-gray-600 dark:text-gray-400 mt-4">Loading analytics data...</p>
                </div>

                <!-- Analytics Content -->
                <div v-else-if="analyticsData" class="space-y-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                        {{ analyticsData.summary.total_views }}
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faEye" class="text-blue-500 text-2xl" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Visitors</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                        {{ analyticsData.summary.unique_visitors }}
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faUsers" class="text-green-500 text-2xl" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Returning Rate</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                        {{ analyticsData.visitor_stats.total_visitors > 0 ? 
                                            Math.round((analyticsData.visitor_stats.returning_visitors / analyticsData.visitor_stats.total_visitors) * 100) : 0 }}%
                                    </p>
                                </div>
                                <FontAwesomeIcon :icon="faChartLine" class="text-purple-500 text-2xl" />
                            </div>
                        </div>
                    </div>

                    <!-- Page Performance -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Page Performance</h4>
                        <div class="space-y-4">
                            <div 
                                v-for="page in analyticsData.page_stats" 
                                :key="page.page"
                                class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                            >
                                <div class="flex items-center space-x-4">
                                    <FontAwesomeIcon 
                                        :icon="PAGE_ICONS[page.page] || faFileAlt" 
                                        class="text-blue-500 text-lg" 
                                    />
                                    <span class="font-medium text-gray-900 dark:text-white capitalize">
                                        {{ page.page }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Views</p>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ page.views }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Unique Views</p>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ page.unique_views }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Analytics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Visitor Types -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Visitor Types</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">New Visitors</span>
                                    <span class="font-bold text-green-600 dark:text-green-400">
                                        {{ analyticsData.visitor_stats.new_visitors }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Returning Visitors</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400">
                                        {{ analyticsData.visitor_stats.returning_visitors }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-semibold">Total Visitors</span>
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ analyticsData.visitor_stats.total_visitors }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Time Series Chart -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Views Over Time</h4>
                            <div class="h-48 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="text-center">
                                    <FontAwesomeIcon :icon="faChartLine" class="text-4xl mb-2 opacity-50" />
                                    <p>Chart visualization would be implemented here</p>
                                    <p class="text-sm mt-2">({{ analyticsData.time_series?.length || 0 }} data points)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Data State -->
                <div v-else class="text-center py-12">
                    <FontAwesomeIcon :icon="faChartLine" class="text-4xl text-gray-400 mb-4" />
                    <h3 class="text-xl font-bold text-gray-600 dark:text-gray-400 mb-2">No Analytics Data Yet</h3>
                    <p class="text-gray-500 dark:text-gray-500">Your analytics data will appear here once you start receiving visitors.</p>
                </div>
            </div>

            <!-- Pages Tab -->
            <div v-if="activeTab === 'pages'" class="group bg-white dark:bg-gray-800 shadow rounded-2xl hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center space-x-3">
                        <FontAwesomeIcon :icon="faFileAlt" class="text-blue-500 text-2xl" />
                        <span>Manage Pages</span>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div 
                        v-for="(status, page) in pageStatus" 
                        :key="page"
                        class="group/item px-6 py-5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-300"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center group-hover/item:scale-110 transition-transform duration-300">
                                <FontAwesomeIcon 
                                    :icon="PAGE_ICONS[page] || faFileAlt" 
                                    class="text-white text-lg" 
                                />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white capitalize">{{ page }} Page</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ status.title }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <Link 
                                :href="route('website.editor', { page })"
                                class="group/btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-2"
                            >
                                <FontAwesomeIcon :icon="faEdit" class="group-hover/btn:rotate-12 transition-transform" />
                                <span>Edit Content</span>
                            </Link>
                            <button
                                @click="togglePublish(page, status.published)"
                                :class="[
                                    'group/btn px-4 py-2 rounded-lg text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-2',
                                    status.published 
                                        ? 'bg-green-600 hover:bg-green-700 text-white' 
                                        : 'bg-gray-600 hover:bg-gray-700 text-white'
                                ]"
                            >
                                <FontAwesomeIcon :icon="status.published ? faToggleOn : faToggleOff" class="group-hover/btn:scale-110 transition-transform" />
                                <span>{{ status.published ? 'Published' : 'Set Live' }}</span>
                            </button>
                            <Link 
                                :href="route('website.page', { page })"
                                target="_blank"
                                class="group/btn text-blue-600 hover:text-blue-800 text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1"
                            >
                                <FontAwesomeIcon :icon="faExternalLinkAlt" class="group-hover/btn:translate-x-1 transition-transform" />
                                <span>Preview</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customization Tab -->
            <div v-if="activeTab === 'customization'" class="group bg-white dark:bg-gray-800 shadow rounded-2xl p-6 hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center space-x-3">
                    <FontAwesomeIcon :icon="faPalette" class="text-purple-500 text-2xl" />
                    <span>Customize Colors</span>
                </h3>
                
                <!-- Color Palettes -->
                <div class="mb-8">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Choose a Color Palette</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div
                            v-for="(palette, key) in colorPalettes"
                            :key="key"
                            @click="selectPalette(key)"
                            :class="[
                                'group/palette border-2 rounded-xl p-4 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-lg',
                                colorForm.colorPalette === key 
                                    ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800 bg-blue-50 dark:bg-blue-900/20' 
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'
                            ]"
                        >
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-3 group-hover/palette:text-blue-600 dark:group-hover/palette:text-blue-400 transition-colors">
                                {{ palette.name }}
                            </h5>
                            <div class="flex space-x-2">
                                <div
                                    v-for="(color, colorKey) in palette.colors"
                                    :key="colorKey"
                                    class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 group-hover/palette:scale-110 transition-transform duration-300"
                                    :style="{ backgroundColor: color }"
                                    :title="`${colorKey}: ${color}`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Color Editor -->
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Custom Colors</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div 
                            v-for="(color, key) in colorForm.customColors" 
                            :key="key" 
                            class="group/color"
                        >
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">
                                {{ key }} Color
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    type="color"
                                    v-model="colorForm.customColors[key]"
                                    class="w-12 h-12 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer group-hover/color:scale-110 transition-transform duration-300"
                                />
                                <input
                                    type="text"
                                    v-model="colorForm.customColors[key]"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                    placeholder="#000000"
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        @click="updateColors"
                        class="group/btn bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105 font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl"
                    >
                        <FontAwesomeIcon :icon="faSave" class="group-hover/btn:rotate-12 transition-transform" />
                        <span>Save Color Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>