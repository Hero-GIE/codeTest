<script setup>
  import { ref, onMounted, onUnmounted, watch } from 'vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faCompass,
    faEye,
    faFileAlt,
    faChartBar,
    faCheckCircle,
    faEdit,
    faToggleOn,
    faToggleOff,
    faPalette,
    faSave,
    faHome,
    faUser,
    faImages,
    faCalendar,
    faEnvelope,
    faSignOutAlt,
    faExternalLinkAlt,
    faDownload,
    faUsers,
    faChartLine,
    faArrowUp,
    faSpinner,
    faBars,
    faTimes,
    faRefresh,
  } from '@fortawesome/free-solid-svg-icons';
  import { Chart, registerables } from 'chart.js';
  import LogoutDialog from './Auth/Logout.vue';

  // Register Chart.js components
  Chart.register(...registerables);

  // Props
  const props = defineProps({
    user: Object,
    websiteSettings: Object,
    analytics: Object,
    colorPalettes: Object,
    pageStatus: Object,
  });

  const activeTab = ref('overview');
  const analyticsData = ref(null);
  const analyticsPeriod = ref('7days');
  const loadingAnalytics = ref(false);
  const exportLoading = ref(false);
  const overviewLoading = ref(true);
  const showLogoutDialog = ref(false);
  const mobileMenuOpen = ref(false);
  const profileDropdownOpen = ref(false);

  // Chart references
  const chartCanvas = ref(null);
  const pagePerformanceChartCanvas = ref(null);
  const chartInstance = ref(null);
  const pagePerformanceChartInstance = ref(null);

  // FIXED: Add the missing reactive variable
  const showProfileEditor = ref(false);
  const editingProfile = ref(false);

  // Update the profileForm initialization to handle different user data structures
  const profileForm = ref({
    name: props.user?.name || props.user?.displayName || props.user?.email?.split('@')[0] || '',
    email: props.user?.email || '',
  });

  // Update the editProfile method to keep dropdown open
  const editProfile = () => {
    profileForm.value = {
      name: props.user?.name || '',
      email: props.user?.email || '',
    };
    showProfileEditor.value = true;
    // Keep the dropdown open when switching to editor
    profileDropdownOpen.value = true;
    mobileMenuOpen.value = true; // Keep mobile menu open too
  };

  // Update the saveProfile method to close dropdown after saving
  const saveProfile = async () => {
    editingProfile.value = true;
    try {
      await router.post('/dashboard/profile', profileForm.value);
      showProfileEditor.value = false;
      // Close the dropdown after successful save
      profileDropdownOpen.value = false;
      mobileMenuOpen.value = false;

      // Refresh the page to get updated user data
      router.reload();
    } catch (error) {
      console.error('Error updating profile:', error);
    } finally {
      editingProfile.value = false;
    }
  };

  // Add this method to prevent dropdown closing during save
  const handleDropdownClick = (event) => {
    // If we're in the middle of saving, don't close the dropdown
    if (editingProfile.value) {
      event.stopPropagation();
    }
  };

  // Update the cancelEdit method to close the entire dropdown
  const cancelEdit = () => {
    showProfileEditor.value = false;
    profileForm.value = {
      name: props.user?.name || '',
      email: props.user?.email || '',
    };
    // Close the entire dropdown
    profileDropdownOpen.value = false;
    mobileMenuOpen.value = false;
  };

  // Add this method to close the dropdown when clicking outside
  const closeProfileDropdown = () => {
    profileDropdownOpen.value = false;
  };

  // Fix logout dialog method
  const openLogoutDialog = () => {
    profileDropdownOpen.value = false; // Close profile dropdown
    mobileMenuOpen.value = false; // Close mobile menu
    showLogoutDialog.value = true;
  };

  const closeLogoutDialog = () => {
    showLogoutDialog.value = false;
  };

  const colorForm = ref({
    colorPalette: props.websiteSettings?.colorPalette || 'default',
    customColors: props.websiteSettings?.customColors || {},
  });

  const PERIOD_OPTIONS = [
    { value: 'today', label: 'Today' },
    { value: '7days', label: 'Last 7 Days' },
    { value: '30days', label: 'Last 30 Days' },
    { value: '90days', label: 'Last 90 Days' },
  ];

  const TAB_CONFIG = {
    overview: { icon: faChartBar, label: 'Overview' },
    pages: { icon: faFileAlt, label: 'Pages' },
    customization: { icon: faPalette, label: 'Customization' },
    analytics: { icon: faChartLine, label: 'Analytics' },
  };

  const PAGE_ICONS = {
    home: faHome,
    about: faUser,
    gallery: faImages,
    contact: faEnvelope,
  };

  // Chart initialization methods
  const initializeChart = () => {
    if (!analyticsData.value?.time_series || analyticsData.value.time_series.length === 0) {
      return;
    }

    // Destroy existing chart if it exists
    if (chartInstance.value) {
      chartInstance.value.destroy();
    }

    const ctx = chartCanvas.value?.getContext('2d');
    if (!ctx) return;

    const timeSeriesData = analyticsData.value.time_series;

    // Prepare data for chart
    const labels = timeSeriesData.map((item) => {
      const date = new Date(item.date);
      return analyticsPeriod.value === 'today'
        ? date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
        : date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });

    const viewsData = timeSeriesData.map((item) => item.views);
    const uniqueVisitorsData = timeSeriesData.map((item) => item.unique_visitors);

    chartInstance.value = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Page Views',
            data: viewsData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
          {
            label: 'Unique Visitors',
            data: uniqueVisitorsData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 20,
              font: {
                size: 12,
                weight: '600',
              },
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            cornerRadius: 8,
            usePointStyle: true,
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.parsed.y.toLocaleString();
                return label;
              },
            },
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              maxRotation: 0,
              font: {
                size: 11,
              },
              color: '#6b7280',
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)',
              drawBorder: false,
            },
            ticks: {
              font: {
                size: 11,
              },
              color: '#6b7280',
              callback: function (value) {
                return value.toLocaleString();
              },
            },
          },
        },
        animation: {
          duration: 1000,
          easing: 'easeOutQuart',
        },
      },
    });
  };

  const initializePagePerformanceChart = () => {
    if (!analyticsData.value?.page_stats || analyticsData.value.page_stats.length === 0) {
      return;
    }

    // Destroy existing chart if it exists
    if (pagePerformanceChartInstance.value) {
      pagePerformanceChartInstance.value.destroy();
    }

    const ctx = pagePerformanceChartCanvas.value?.getContext('2d');
    if (!ctx) return;

    const pageStats = analyticsData.value.page_stats;
    const labels = pageStats.map((page) => {
      const pageName = page.page.charAt(0).toUpperCase() + page.page.slice(1);
      return pageName.length > 8 ? pageName.substring(0, 8) + '...' : pageName;
    });

    const viewsData = pageStats.map((page) => page.views);
    const uniqueViewsData = pageStats.map((page) => page.unique_views);

    pagePerformanceChartInstance.value = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Total Views',
            data: viewsData,
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
          },
          {
            label: 'Unique Views',
            data: uniqueViewsData,
            backgroundColor: 'rgba(16, 185, 129, 0.7)',
            borderColor: '#10b981',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                size: 11,
                weight: '600',
              },
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 10,
            cornerRadius: 6,
            usePointStyle: true,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              font: {
                size: 10,
              },
              color: '#6b7280',
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)',
              drawBorder: false,
            },
            ticks: {
              font: {
                size: 10,
              },
              color: '#6b7280',
              callback: function (value) {
                return value.toLocaleString();
              },
            },
          },
        },
        animation: {
          duration: 1000,
          easing: 'easeOutQuart',
        },
      },
    });
  };

  const fetchAnalytics = async (period = '7days') => {
    loadingAnalytics.value = true;
    overviewLoading.value = true;
    try {
      console.log(`🔄 Fetching analytics for period: ${period}`);

      const response = await fetch(`/dashboard/analytics?period=${period}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
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
        stack: error.stack,
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
      ...(props.colorPalettes[paletteKey]?.colors || {}),
    };
  };

  const togglePublish = async (page, currentStatus) => {
    try {
      await router.post(`/dashboard/pages/${page}/publish`, {
        published: !currentStatus,
      });
    } catch (error) {
      console.error('Error toggling publish:', error);
    }
  };

  // Computed properties
  const publishedPagesCount = () => {
    return Object.values(props.pageStatus || {}).filter((page) => page.published).length;
  };

  const totalPagesCount = () => {
    return Object.keys(props.pageStatus || {}).length;
  };

  // Watch for analytics data changes and update charts
  watch(
    () => analyticsData.value,
    (newData) => {
      if (newData && newData.time_series && newData.time_series.length > 0) {
        // Small delay to ensure DOM is updated
        setTimeout(() => {
          initializeChart();
        }, 100);
      }

      if (newData && newData.page_stats && newData.page_stats.length > 0) {
        setTimeout(() => {
          initializePagePerformanceChart();
        }, 150);
      }
    },
    { deep: true }
  );

  // Lifecycle
  onMounted(() => {
    fetchAnalytics(analyticsPeriod.value);
  });

  // Add click outside handler
  const handleClickOutside = (event) => {
    if (profileDropdownOpen.value && !event.target.closest('.relative')) {
      profileDropdownOpen.value = false;
    }
  };

  // Add event listener when component mounts
  onMounted(() => {
    document.addEventListener('click', handleClickOutside);
  });

  // Remove event listener when component unmounts
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);

    // Clean up charts
    if (chartInstance.value) {
      chartInstance.value.destroy();
    }
    if (pagePerformanceChartInstance.value) {
      pagePerformanceChartInstance.value.destroy();
    }
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
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-2">
            <!-- View Live Site Button -->
            <Link
              :href="route('website.home')"
              class="group flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm border border-gray-200 dark:border-gray-600"
              target="_blank"
            >
              <FontAwesomeIcon
                :icon="faExternalLinkAlt"
                class="text-sm group-hover:scale-110 transition-transform"
              />
              <span>View Live Site</span>
            </Link>

            <!-- User Profile Dropdown -->
            <div class="relative">
              <button
                @click="profileDropdownOpen = !profileDropdownOpen"
                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
              >
                <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center">
                  <FontAwesomeIcon :icon="faUser" class="text-white text-sm" />
                </div>
                <span
                  class="text-sm font-medium text-gray-700 dark:text-gray-200 max-w-32 truncate"
                >
                  {{ user?.name || user?.email || 'User' }}
                </span>
                <svg
                  class="w-4 h-4 text-gray-500 transition-transform duration-200"
                  :class="{
                    'rotate-180': profileDropdownOpen,
                  }"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="profileDropdownOpen"
                class="absolute right-0 mt-3 w-96 bg-white border-2 border-black rounded-2xl shadow-2xl z-50 overflow-hidden"
                @click="handleDropdownClick"
              >
                <!-- Animated Content Container -->
                <div class="relative">
                  <!-- Profile View (Default) -->
                  <div v-show="!showProfileEditor" class="transition-all duration-300 ease-in-out">
                    <!-- Header -->
                    <div class="p-6 bg-black border-b border-gray-800">
                      <div class="flex items-center space-x-4">
                        <div
                          class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center ring-2 ring-white"
                        >
                          <FontAwesomeIcon :icon="faUser" class="text-black text-lg font-bold" />
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-lg font-bold text-white truncate">
                            {{
                              user?.name ||
                              user?.displayName ||
                              user?.email?.split('@')[0] ||
                              'User'
                            }}
                          </p>
                          <p class="text-sm text-white truncate mt-1">
                            {{ user?.email || 'No email' }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- User Stats -->
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                      <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                          <div
                            class="w-10 h-10 bg-black rounded-lg flex items-center justify-center mx-auto mb-2"
                          >
                            <FontAwesomeIcon :icon="faCalendar" class="text-white text-sm" />
                          </div>
                          <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">
                            Member Since
                          </p>
                          <p class="text-sm font-bold text-black mt-1">
                            {{
                              user?.created_at
                                ? new Date(user.created_at).toLocaleDateString('en-US', {
                                    month: 'short',
                                    year: 'numeric',
                                  })
                                : 'N/A'
                            }}
                          </p>
                        </div>
                        <div class="text-center">
                          <div
                            class="w-10 h-10 bg-black rounded-lg flex items-center justify-center mx-auto mb-2"
                          >
                            <FontAwesomeIcon :icon="faCheckCircle" class="text-white text-sm" />
                          </div>
                          <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">
                            Status
                          </p>
                          <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-black text-white mt-1"
                          >
                            <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                            Verified
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="p-2">
                      <div class="space-y-1">
                        <button
                          @click="editProfile"
                          class="flex items-center space-x-3 w-full px-4 py-3 text-sm font-bold text-black hover:bg-black hover:text-white rounded-xl transition-all duration-300 group"
                        >
                          <div
                            class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white group-hover:scale-110 transition-all duration-300"
                          >
                            <FontAwesomeIcon
                              :icon="faEdit"
                              class="text-gray-600 text-xs group-hover:text-black transition-colors duration-300"
                            />
                          </div>
                          <span>Edit Profile</span>
                          <FontAwesomeIcon
                            :icon="faExternalLinkAlt"
                            class="text-gray-400 text-xs ml-auto group-hover:text-white transition-colors duration-300"
                          />
                        </button>

                        <Link
                          :href="route('website.home')"
                          target="_blank"
                          @click="
                            profileDropdownOpen = false;
                            mobileMenuOpen = false;
                          "
                          class="flex items-center space-x-3 w-full px-4 py-3 text-sm font-bold text-black hover:bg-black hover:text-white rounded-xl transition-all duration-300 group"
                        >
                          <div
                            class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white group-hover:scale-110 transition-all duration-300"
                          >
                            <FontAwesomeIcon
                              :icon="faExternalLinkAlt"
                              class="text-gray-600 text-xs group-hover:text-black transition-colors duration-300"
                            />
                          </div>
                          <span>View Live Site</span>
                          <FontAwesomeIcon
                            :icon="faExternalLinkAlt"
                            class="text-gray-400 text-xs ml-auto group-hover:text-white transition-colors duration-300"
                          />
                        </Link>
                      </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                      <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600 font-medium">Account ID</span>
                        <span class="font-mono font-bold text-black bg-gray-200 px-2 py-1 rounded">
                          {{ user?.uid?.substring(0, 8) || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Profile Editor -->
                  <div v-show="showProfileEditor" class="transition-all duration-300 ease-in-out">
                    <!-- Editor Header -->
                    <div class="p-6 bg-black border-b border-gray-800">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                          <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <FontAwesomeIcon :icon="faEdit" class="text-black text-sm font-bold" />
                          </div>
                          <div>
                            <h3 class="text-lg font-bold text-white">Edit Profile</h3>
                            <p class="text-sm text-white mt-1">Update your personal information</p>
                          </div>
                        </div>
                        <button
                          @click="cancelEdit"
                          class="text-gray-400 hover:text-white transition-all duration-300 p-2 rounded-lg hover:bg-gray-800"
                        >
                          <FontAwesomeIcon :icon="faTimes" class="w-4 h-4" />
                        </button>
                      </div>
                    </div>

                    <!-- Editor Form -->
                    <form @submit.prevent="saveProfile" class="p-6 space-y-6 bg-white">
                      <!-- Name Field -->
                      <div class="group">
                        <label
                          class="block text-sm font-bold text-black mb-3 uppercase tracking-wide text-xs"
                        >
                          Your Name
                        </label>
                        <div class="relative">
                          <input
                            type="text"
                            v-model="profileForm.name"
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 bg-white text-black placeholder-gray-500 focus:border-black focus:ring-2 focus:ring-black focus:ring-opacity-20 transition-all duration-300 text-base font-medium"
                            placeholder="Enter your full name"
                            required
                          />
                          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <FontAwesomeIcon :icon="faUser" class="text-gray-400 text-sm" />
                          </div>
                        </div>
                      </div>

                      <!-- Email Field -->
                      <div class="group">
                        <label
                          class="block text-sm font-bold text-black mb-3 uppercase tracking-wide text-xs"
                        >
                          Email Address
                        </label>
                        <div class="relative">
                          <input
                            type="email"
                            v-model="profileForm.email"
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 bg-white text-black placeholder-gray-500 focus:border-black focus:ring-2 focus:ring-black focus:ring-opacity-20 transition-all duration-300 text-base font-medium"
                            placeholder="your@email.com"
                            required
                          />
                          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <FontAwesomeIcon :icon="faEnvelope" class="text-gray-400 text-sm" />
                          </div>
                        </div>
                      </div>

                      <!-- Action Buttons -->
                      <div class="flex space-x-3 pt-4">
                        <button
                          type="button"
                          @click="cancelEdit"
                          class="flex-1 px-6 py-3 border-2 border-black text-black rounded-xl hover:bg-black hover:text-white transition-all duration-300 font-bold text-sm uppercase tracking-wide flex items-center justify-center space-x-2 group"
                        >
                          <FontAwesomeIcon
                            :icon="faTimes"
                            class="w-3 h-3 group-hover:rotate-90 transition-transform duration-300"
                          />
                          <span>Cancel</span>
                        </button>
                        <button
                          type="submit"
                          :disabled="editingProfile"
                          class="flex-1 px-6 py-3 bg-black text-white rounded-xl hover:bg-gray-800 transition-all duration-300 font-bold text-sm uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 group"
                          :class="{
                            'hover:scale-105': !editingProfile,
                            'opacity-70 cursor-wait': editingProfile,
                          }"
                        >
                          <FontAwesomeIcon
                            v-if="editingProfile"
                            :icon="faSpinner"
                            class="animate-spin w-3 h-3"
                          />
                          <FontAwesomeIcon
                            v-else
                            :icon="faSave"
                            class="w-3 h-3 group-hover:scale-110 transition-transform duration-300"
                          />
                          <span>{{ editingProfile ? 'Saving...' : 'Save' }}</span>
                        </button>
                      </div>

                      <!-- Quick Tips -->
                      <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start space-x-3">
                          <div
                            class="w-5 h-5 bg-black rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                          >
                            <FontAwesomeIcon :icon="faCheckCircle" class="text-white text-xs" />
                          </div>
                          <div>
                            <p class="text-xs font-medium text-gray-800">
                              Your profile information helps personalize your experience across the
                              platform.
                            </p>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Logout Button -->
            <button
              @click="openLogoutDialog"
              class="group flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm border border-gray-200 dark:border-gray-600"
            >
              <FontAwesomeIcon
                :icon="faSignOutAlt"
                class="text-sm group-hover:scale-110 transition-transform"
              />
              <span>Logout</span>
            </button>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
          <!-- Mobile User Info with Editor -->
          <div class="relative">
            <!-- Profile View -->
            <div v-show="!showProfileEditor" class="transition-all duration-300 ease-in-out">
              <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div
                  class="w-8 h-8 bg-black rounded-full flex items-center justify-center flex-shrink-0"
                >
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
            </div>

            <!-- Profile Editor for Mobile -->
            <div v-show="showProfileEditor" class="transition-all duration-300 ease-in-out">
              <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Profile</h3>
                  <button
                    @click="cancelEdit"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded"
                  >
                    <FontAwesomeIcon :icon="faTimes" class="w-4 h-4" />
                  </button>
                </div>

                <form @submit.prevent="saveProfile" class="space-y-3">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Name
                    </label>
                    <input
                      type="text"
                      v-model="profileForm.name"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 text-sm"
                      placeholder="Your name"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Email
                    </label>
                    <input
                      type="email"
                      v-model="profileForm.email"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 text-sm"
                      placeholder="your@email.com"
                      required
                    />
                  </div>

                  <div class="flex space-x-2 pt-1">
                    <button
                      type="submit"
                      :disabled="editingProfile"
                      class="flex-1 px-6 py-3 bg-black text-white rounded-xl hover:bg-gray-800 transition-all duration-300 font-bold text-sm uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 group"
                      :class="{
                        'hover:scale-105': !editingProfile,
                        'opacity-70 cursor-wait': editingProfile,
                      }"
                    >
                      <FontAwesomeIcon
                        v-if="editingProfile"
                        :icon="faSpinner"
                        class="animate-spin w-3 h-3"
                      />
                      <FontAwesomeIcon
                        v-else
                        :icon="faSave"
                        class="w-3 h-3 group-hover:scale-110 transition-transform duration-300"
                      />
                      <span>{{ editingProfile ? 'Saving...' : 'Save' }}</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Only show these buttons when NOT editing profile -->
          <div v-show="!showProfileEditor" class="space-y-2">
            <!-- In Mobile Navigation Menu - update View Live Site link -->
            <Link
              :href="route('website.home')"
              @click="
                mobileMenuOpen = false;
                profileDropdownOpen = false;
              "
              class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
              target="_blank"
            >
              <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-sm" />
              <span>View Live Site</span>
            </Link>
            <button
              @click="editProfile"
              class="w-full flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-left"
            >
              <FontAwesomeIcon :icon="faEdit" class="text-sm" />
              <span>Edit Profile</span>
            </button>
            <button
              @click="openLogoutDialog"
              class="w-full flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-left"
            >
              <FontAwesomeIcon :icon="faSignOutAlt" class="text-sm" />
              <span>Logout</span>
            </button>
          </div>
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
      <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto scrollbar-hide">
          <button
            v-for="(tabConfig, tabKey) in TAB_CONFIG"
            :key="tabKey"
            @click="activeTab = tabKey"
            :class="[
              'group whitespace-nowrap py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-xs sm:text-sm transition-all duration-300 flex items-center space-x-1 sm:space-x-2 min-w-max',
              activeTab === tabKey
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
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
          <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
          >
            <div class="p-4 sm:p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Total Visitors
                  </p>

                  <!-- Skeleton Loading -->
                  <div v-if="overviewLoading" class="mt-2 space-y-2">
                    <div
                      class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                    ></div>
                    <div
                      class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"
                    ></div>
                  </div>

                  <!-- Actual Content -->
                  <div v-else>
                    <p
                      class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                    >
                      {{ analyticsData?.summary?.unique_visitors || 0 }}
                    </p>
                    <p
                      class="text-xs sm:text-sm text-green-600 dark:text-green-400 flex items-center mt-1"
                    >
                      <FontAwesomeIcon :icon="faArrowUp" class="text-xs mr-1" />
                      <span>All Time</span>
                    </p>
                  </div>
                </div>
                <div
                  class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg"
                >
                  <FontAwesomeIcon
                    :icon="faUsers"
                    class="text-white text-sm sm:text-base lg:text-lg"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Page Views Card -->
          <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
          >
            <div class="p-4 sm:p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Page Views
                  </p>

                  <!-- Skeleton Loading -->
                  <div v-if="overviewLoading" class="mt-2 space-y-2">
                    <div
                      class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                    ></div>
                    <div
                      class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"
                    ></div>
                  </div>

                  <!-- Actual Content -->
                  <div v-else>
                    <p
                      class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                    >
                      {{ analyticsData?.summary?.total_views || 0 }}
                    </p>
                    <p
                      class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 flex items-center mt-1"
                    >
                      <FontAwesomeIcon :icon="faChartLine" class="text-xs mr-1" />
                      <span>All Time</span>
                    </p>
                  </div>
                </div>
                <div
                  class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg"
                >
                  <FontAwesomeIcon
                    :icon="faEye"
                    class="text-white text-sm sm:text-base lg:text-lg"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Visitor Types Card -->
          <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
          >
            <div class="p-4 sm:p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Visitor Types
                  </p>

                  <!-- Skeleton Loading -->
                  <div v-if="overviewLoading" class="mt-2 space-y-1">
                    <div class="space-y-1">
                      <div
                        class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                      ></div>
                      <div
                        class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                      ></div>
                    </div>
                  </div>

                  <!-- Actual Content -->
                  <div v-else class="mt-1 sm:mt-2 space-y-1">
                    <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                      New:
                      {{ analyticsData?.visitor_stats?.new_visitors || 0 }}
                    </p>
                    <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                      Returning:
                      {{ analyticsData?.visitor_stats?.returning_visitors || 0 }}
                    </p>
                  </div>
                </div>
                <div
                  class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg"
                >
                  <FontAwesomeIcon
                    :icon="faUsers"
                    class="text-white text-sm sm:text-base lg:text-lg"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Published Pages Card -->
          <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg sm:rounded-xl hover:shadow-lg sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
          >
            <div class="p-4 sm:p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Published Pages
                  </p>

                  <!-- Skeleton Loading -->
                  <div v-if="overviewLoading" class="mt-2 space-y-2">
                    <div
                      class="h-6 sm:h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                    ></div>
                    <div
                      class="h-3 sm:h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 sm:w-20"
                    ></div>
                  </div>

                  <!-- Actual Content -->
                  <div v-else>
                    <p
                      class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                    >
                      {{ publishedPagesCount() }}/{{ totalPagesCount() }}
                    </p>
                    <p
                      class="text-xs sm:text-sm text-orange-600 dark:text-orange-400 flex items-center mt-1"
                    >
                      <FontAwesomeIcon :icon="faCheckCircle" class="text-xs mr-1" />
                      <span>Live</span>
                    </p>
                  </div>
                </div>
                <div
                  class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-105 sm:group-hover:scale-110 transition-transform duration-300 shadow-lg"
                >
                  <FontAwesomeIcon
                    :icon="faCheckCircle"
                    class="text-white text-sm sm:text-base lg:text-lg"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div
          class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700"
        >
          <h3
            class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center space-x-2 sm:space-x-3"
          >
            <div
              class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center"
            >
              <FontAwesomeIcon
                :icon="faCompass"
                class="text-white text-sm sm:text-base lg:text-lg"
              />
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
                <div
                  class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover/card:scale-105 sm:group-hover/card:scale-110 transition-transform duration-300"
                >
                  <FontAwesomeIcon
                    :icon="PAGE_ICONS[page]"
                    class="text-white text-sm sm:text-base"
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <h4
                    class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm capitalize truncate"
                  >
                    Edit {{ page }} Page
                  </h4>
                  <p
                    class="text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1 mt-1"
                  >
                    <FontAwesomeIcon
                      :icon="faCheckCircle"
                      class="text-green-500 text-xs"
                      v-if="pageStatus[page]?.published"
                    />
                    <span class="truncate">
                      {{ pageStatus[page]?.published ? 'Published' : 'Draft' }}
                    </span>
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
        <div
          class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
        >
          <div
            class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
          >
            <div class="flex-1 min-w-0">
              <h3
                class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2 sm:space-x-3"
              >
                <FontAwesomeIcon
                  :icon="faChartLine"
                  class="text-green-500 text-lg sm:text-xl lg:text-2xl"
                />
                <span class="truncate">Website Analytics</span>
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mt-1 text-xs sm:text-sm">
                Track your website performance and visitor behavior
              </p>
            </div>
            <div
              class="flex flex-col xs:flex-row items-stretch xs:items-center space-y-2 xs:space-y-0 xs:space-x-3 sm:space-x-4"
            >
              <select
                v-model="analyticsPeriod"
                @change="fetchAnalytics(analyticsPeriod)"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm"
              >
                <option v-for="option in PERIOD_OPTIONS" :key="option.value" :value="option.value">
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
          <FontAwesomeIcon
            :icon="faSpinner"
            class="text-3xl sm:text-4xl text-blue-500 animate-spin"
          />
          <p class="text-gray-600 dark:text-gray-400 mt-3 sm:mt-4 text-sm sm:text-base">
            Loading analytics data...
          </p>
        </div>

        <!-- Analytics Content -->
        <div v-else-if="analyticsData" class="space-y-4 sm:space-y-6">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
            <div
              class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Total Views
                  </p>
                  <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                  >
                    {{ analyticsData.summary.total_views }}
                  </p>
                </div>
                <FontAwesomeIcon
                  :icon="faEye"
                  class="text-blue-500 text-lg sm:text-xl lg:text-2xl"
                />
              </div>
            </div>
            <div
              class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Unique Visitors
                  </p>
                  <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                  >
                    {{ analyticsData.summary.unique_visitors }}
                  </p>
                </div>
                <FontAwesomeIcon
                  :icon="faUsers"
                  class="text-green-500 text-lg sm:text-xl lg:text-2xl"
                />
              </div>
            </div>
            <div
              class="bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                    Returning Rate
                  </p>
                  <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2"
                  >
                    {{
                      analyticsData.visitor_stats.total_visitors > 0
                        ? Math.round(
                            (analyticsData.visitor_stats.returning_visitors /
                              analyticsData.visitor_stats.total_visitors) *
                              100
                          )
                        : 0
                    }}%
                  </p>
                </div>
                <FontAwesomeIcon
                  :icon="faChartLine"
                  class="text-purple-500 text-lg sm:text-xl lg:text-2xl"
                />
              </div>
            </div>
          </div>

          <!-- Page Performance with Chart -->
          <div
            class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
          >
            <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
              Page Performance
            </h4>

            <!-- Empty State -->
            <div
              v-if="!analyticsData.page_stats || analyticsData.page_stats.length === 0"
              class="text-center py-8 sm:py-12"
            >
              <div
                class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <FontAwesomeIcon :icon="faChartLine" class="text-blue-500 text-2xl sm:text-3xl" />
              </div>
              <h5 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2">
                No Page Data Yet
              </h5>
              <p
                class="text-gray-500 dark:text-gray-400 text-sm sm:text-base max-w-md mx-auto mb-6"
              >
                Your page performance data will appear here once visitors start exploring your
                website.
              </p>
            </div>

            <!-- Page Performance with Bar Chart -->
            <div v-else class="space-y-6">
              <!-- Bar Chart -->
              <div class="h-48 sm:h-56 lg:h-64">
                <canvas ref="pagePerformanceChartCanvas" class="w-full h-full"></canvas>
              </div>

              <!-- Detailed Stats -->
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
                    <span
                      class="font-medium text-gray-900 dark:text-white capitalize truncate text-xs sm:text-sm"
                    >
                      {{ page.page }}
                    </span>
                  </div>
                  <div class="flex items-center space-x-3 sm:space-x-4 ml-2">
                    <div class="text-right">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                      <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                        {{ page.views }}
                      </p>
                    </div>
                    <div class="text-right">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Unique</p>
                      <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                        {{ page.unique_views }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Visitor Analytics -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <!-- Visitor Types -->
            <div
              class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
            >
              <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                Visitor Types
              </h4>
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
                <div
                  class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                  <span class="text-gray-900 dark:text-white font-semibold text-sm">
                    Total Visitors
                  </span>
                  <span class="font-bold text-gray-900 dark:text-white text-sm">
                    {{ analyticsData.visitor_stats.total_visitors }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Time Series Chart -->
            <div
              class="bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700"
            >
              <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                Views Over Time
              </h4>
              <div class="h-64 sm:h-72 lg:h-80">
                <canvas
                  ref="chartCanvas"
                  v-if="analyticsData?.time_series?.length > 0"
                  class="w-full h-full"
                ></canvas>
                <div
                  v-else
                  class="h-full flex items-center justify-center text-gray-500 dark:text-gray-400"
                >
                  <div class="text-center">
                    <FontAwesomeIcon
                      :icon="faChartLine"
                      class="text-2xl sm:text-3xl lg:text-4xl mb-2 opacity-50"
                    />
                    <p class="text-xs sm:text-sm">No time series data available</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Data State -->
        <div v-else class="text-center py-8 sm:py-12">
          <FontAwesomeIcon
            :icon="faChartLine"
            class="text-3xl sm:text-4xl text-gray-400 mb-3 sm:mb-4"
          />
          <h3 class="text-lg sm:text-xl font-bold text-gray-600 dark:text-gray-400 mb-2">
            No Analytics Data Yet
          </h3>
          <p class="text-gray-500 dark:text-gray-500 text-sm sm:text-base">
            Your analytics data will appear here once you start receiving visitors.
          </p>
        </div>
      </div>

      <!-- Pages Tab -->
      <div
        v-if="activeTab === 'pages'"
        class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700"
      >
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-gray-700">
          <h3
            class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center space-x-2 sm:space-x-3"
          >
            <FontAwesomeIcon
              :icon="faFileAlt"
              class="text-blue-500 text-lg sm:text-xl lg:text-2xl"
            />
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
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg sm:rounded-xl flex items-center justify-center group-hover/item:scale-105 sm:group-hover/item:scale-110 transition-transform duration-300 flex-shrink-0"
              >
                <FontAwesomeIcon
                  :icon="PAGE_ICONS[page] || faFileAlt"
                  class="text-white text-sm sm:text-base"
                />
              </div>
              <div class="min-w-0 flex-1">
                <h4
                  class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base capitalize truncate"
                >
                  {{ page }} Page
                </h4>
                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm truncate">
                  {{ status.title }}
                </p>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 sm:gap-3">
              <Link
                :href="route('website.editor', { page })"
                class="group/btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1 sm:space-x-2"
              >
                <FontAwesomeIcon
                  :icon="faEdit"
                  class="text-xs group-hover/btn:rotate-12 transition-transform"
                />
                <span>Edit</span>
              </Link>
              <button
                @click="togglePublish(page, status.published)"
                :class="[
                  'group/btn px-3 py-2 rounded-lg text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1 sm:space-x-2',
                  status.published
                    ? 'bg-green-600 hover:bg-green-700 text-white'
                    : 'bg-gray-600 hover:bg-gray-700 text-white',
                ]"
              >
                <FontAwesomeIcon
                  :icon="status.published ? faToggleOn : faToggleOff"
                  class="text-xs group-hover/btn:scale-110 transition-transform"
                />
                <span>{{ status.published ? 'Published' : 'Set Live' }}</span>
              </button>
              <Link
                :href="route('website.page', { page })"
                target="_blank"
                class="group/btn text-blue-600 hover:text-blue-800 text-xs sm:text-sm transition-all duration-300 hover:scale-105 flex items-center space-x-1"
              >
                <FontAwesomeIcon
                  :icon="faExternalLinkAlt"
                  class="text-xs group-hover/btn:translate-x-1 transition-transform"
                />
                <span>Preview</span>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Customization Tab -->
      <div
        v-if="activeTab === 'customization'"
        class="group bg-white dark:bg-gray-800 shadow rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:shadow-lg sm:hover:shadow-xl transition-all duration-500 border border-gray-100 dark:border-gray-700"
      >
        <h3
          class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center space-x-2 sm:space-x-3"
        >
          <FontAwesomeIcon
            :icon="faPalette"
            class="text-purple-500 text-lg sm:text-xl lg:text-2xl"
          />
          <span>Customize Colors</span>
        </h3>

        <!-- Color Palettes -->
        <div class="mb-6 sm:mb-8">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 text-sm sm:text-base">
            Choose a Color Palette
          </h4>
          <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div
              v-for="(palette, key) in colorPalettes"
              :key="key"
              @click="selectPalette(key)"
              :class="[
                'group/palette border-2 rounded-lg sm:rounded-xl p-3 sm:p-4 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-lg',
                colorForm.colorPalette === key
                  ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800 bg-blue-50 dark:bg-blue-900/20'
                  : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600',
              ]"
            >
              <h5
                class="font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3 text-xs sm:text-sm group-hover/palette:text-blue-600 dark:group-hover/palette:text-blue-400 transition-colors truncate"
              >
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
          <h4 class="font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 text-sm sm:text-base">
            Custom Colors
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div v-for="(color, key) in colorForm.customColors" :key="key" class="group/color">
              <label
                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2 capitalize"
              >
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
            <FontAwesomeIcon
              :icon="faSave"
              class="group-hover/btn:rotate-12 transition-transform"
            />
            <span>Save Color Changes</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Logout Dialog -->
    <LogoutDialog :show-dialog="showLogoutDialog" @close="closeLogoutDialog" />
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
  img,
  svg {
    max-width: 100%;
    height: auto;
  }

  /* Add smooth transitions for the profile editor */
  .profile-transition-enter-active,
  .profile-transition-leave-active {
    transition: all 0.3s ease;
  }

  .profile-transition-enter-from {
    opacity: 0;
    transform: scale(0.95);
  }

  .profile-transition-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }

  /* Smooth transitions for charts */
  canvas {
    transition: opacity 0.3s ease;
  }

  /* Custom scrollbar for better mobile experience */
  ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }

  /* Dark mode scrollbar */
  .dark ::-webkit-scrollbar-track {
    background: #374151;
  }

  .dark ::-webkit-scrollbar-thumb {
    background: #6b7280;
  }

  .dark ::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }
</style>
