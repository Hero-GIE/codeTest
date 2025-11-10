<script setup>
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faRocket,
    faBook,
    faMapMarkedAlt,
    faCamera,
    faUsers,
    faChartLine,
    faMountain,
    faArrowRight,
    faStar,
    faHeart,
    faCompass,
    faGlobe,
    faMountainSun,
    faRoute,
    faBinoculars,
    faPlus,
    faEdit,
    faSave,
    faTimes,
    faTree,
    faCampground,
    faPlayCircle,
  } from '@fortawesome/free-solid-svg-icons';
  import { ref, computed, watch, onMounted } from 'vue';

  // ✅ FIXED: Define props FIRST and store in a variable
  const props = defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean,
    publishedPages: Array,
    websiteSettings: Object,
  });

  // Local editable content - MOVED UP before computed properties
  const editableContent = ref({});
  const editingSection = ref(null);
  const editingField = ref(null);

  // ✅ FIXED: Now you can use props.websiteSettings
  const themeColors = computed(() => {
    return (
      props.websiteSettings?.customColors || {
        primary: '#000000',
        secondary: '#8B4513',
        accent: '#FFFFFF',
      }
    );
  });

  // Helper function to get CSS variable value
  const getCssVariable = (variableName) => {
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
  };

  // Emit events for saving changes
  const emit = defineEmits(['update:pageContent', 'save']);

  // Default content that matches the initialization structure
  const defaultPageContent = {
    title: 'My Adventure Log',
    published: true,
    sections: {
      hero: {
        badge: 'Welcome to Your Adventure Log',
        title: 'Welcome to My Adventure Log',
        subtitle: 'Documenting my journeys and experiences',
        text: 'Start your adventure and share your stories with the world.',
        backgroundImage:
          'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80',
        cta1Title: 'Explore Adventures',
        cta1Subtitle: 'Start your journey today',
        cta2Title: 'We guide your path',
        cta2Subtitle: 'Get into action',
        stats: [
          { number: '100+', label: 'Adventures', description: 'By our community' },
          { number: '50+', label: 'Countries', description: 'Explored worldwide' },
          { number: '1k+', label: 'Photos', description: 'Memories captured' },
        ],
      },
      features: {
        title: 'What I Do',
        items: [
          {
            title: 'Adventure Logging',
            description: 'Track and share my adventures',
            icon: '🚀',
          },
          {
            title: 'Story Telling',
            description: 'Share experiences and memories',
            icon: '📖',
          },
          {
            title: 'Photo Journal',
            description: 'Visual journey through photos',
            icon: '📷',
          },
        ],
      },
      mission: {
        title: 'Our Mission',
        content:
          'We believe every adventure has a story worth telling. Our mission is to provide the tools and platform for adventurers to document, share, and inspire others with their journeys.',
        stats: [
          { number: '0+', label: 'Adventures Logged' },
          { number: '0+', label: 'Countries Covered' },
          { number: '0+', label: 'Photos Shared' },
          { number: '1', label: 'Happy Explorer' },
        ],
      },
      recent: {
        title: 'Recent Adventures',
        posts: [
          {
            title: 'Mountain Hiking',
            date: '2024-01-15',
            image:
              'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1176&q=80',
            excerpt: 'Amazing views from the summit...',
          },
          {
            title: 'Beach Vacation',
            date: '2024-01-10',
            image:
              'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1173&q=80',
            excerpt: 'Relaxing days by the ocean...',
          },
        ],
      },
    },
  };

  // ✅ FIXED: Initialize with proper fallback to default content
  function initializeEditableContent() {
    console.log('🔄 Initializing content...');
    console.log('📦 Props pageContent:', props.pageContent);

    // Use pageContent if it exists and has content, otherwise use defaultPageContent
    if (
      props.pageContent &&
      props.pageContent.sections &&
      Object.keys(props.pageContent.sections).length > 0
    ) {
      editableContent.value = JSON.parse(JSON.stringify(props.pageContent));
      console.log('✅ Using provided pageContent');
    } else {
      editableContent.value = JSON.parse(JSON.stringify(defaultPageContent));
      console.log('✅ Using defaultPageContent (no props provided)');
    }

    // Ensure all default sections exist
    Object.keys(defaultPageContent.sections).forEach((sectionKey) => {
      if (!editableContent.value.sections[sectionKey]) {
        editableContent.value.sections[sectionKey] = JSON.parse(
          JSON.stringify(defaultPageContent.sections[sectionKey])
        );
      } else {
        // Ensure all fields in each section exist
        Object.keys(defaultPageContent.sections[sectionKey]).forEach((fieldKey) => {
          if (editableContent.value.sections[sectionKey][fieldKey] === undefined) {
            editableContent.value.sections[sectionKey][fieldKey] = JSON.parse(
              JSON.stringify(defaultPageContent.sections[sectionKey][fieldKey])
            );
          }
        });
      }
    });

    console.log('🎯 Final editableContent:', editableContent.value);
  }

  // Watch for changes in pageContent prop
  watch(
    () => props.pageContent,
    () => {
      initializeEditableContent();
    },
    { immediate: true }
  );

  // ✅ MOVED: Now these computed properties can safely access editableContent
  const adventuresPerPage = ref(4);
  const currentAdventurePage = ref(1);
  const loadingMore = ref(false);

  // Computed properties for pagination
  const displayedAdventures = computed(() => {
    const allAdventures = editableContent.value.sections?.recent?.posts || [];
    const startIndex = 0;
    const endIndex = currentAdventurePage.value * adventuresPerPage.value;
    return allAdventures.slice(startIndex, endIndex);
  });

  const hasMoreAdventures = computed(() => {
    const allAdventures = editableContent.value.sections?.recent?.posts || [];
    return displayedAdventures.value.length < allAdventures.length;
  });

  const totalAdventures = computed(() => {
    return editableContent.value.sections?.recent?.posts?.length || 0;
  });

  // Computed property to check if user has any adventures
  const hasAdventures = computed(() => {
    const posts = editableContent.value.sections?.recent?.posts;
    return posts && posts.length > 0;
  });

  // Load more adventures method
  const loadMoreAdventures = () => {
    loadingMore.value = true;

    // Simulate loading delay for better UX
    setTimeout(() => {
      currentAdventurePage.value++;
      loadingMore.value = false;
    }, 500);
  };

  // Reset pagination when adventures change
  watch(
    () => editableContent.value.sections?.recent?.posts,
    () => {
      currentAdventurePage.value = 1;
    },
    { deep: true }
  );

  // Editing methods
  const startEditing = (section, field = null, index = null) => {
    if (!props.isEditMode) return;

    if (index !== null) {
      editingSection.value = `${section}[${index}]`;
    } else {
      editingSection.value = section;
    }
    editingField.value = field;
  };

  const stopEditing = () => {
    editingSection.value = null;
    editingField.value = null;
    // Emit changes when editing stops
    emit('update:pageContent', editableContent.value);
  };

  const saveChanges = () => {
    emit('save', editableContent.value);
    stopEditing();
  };

  // Hero stats methods
  const addHeroStat = () => {
    if (!editableContent.value.sections.hero.stats) {
      editableContent.value.sections.hero.stats = [];
    }
    editableContent.value.sections.hero.stats.push({
      number: '0+',
      label: 'New Stat',
      description: 'By our community',
    });
  };

  const removeHeroStat = (index) => {
    if (editableContent.value.sections.hero.stats) {
      editableContent.value.sections.hero.stats.splice(index, 1);
    }
  };

  const addFeatureItem = () => {
    if (!editableContent.value.sections.features.items) {
      editableContent.value.sections.features.items = [];
    }
    editableContent.value.sections.features.items.push({
      title: 'New Feature',
      description: 'Describe this amazing feature...',
      icon: '🚀',
    });
  };

  const removeFeatureItem = (index) => {
    if (editableContent.value.sections.features.items) {
      editableContent.value.sections.features.items.splice(index, 1);
    }
  };

  const addMissionStat = () => {
    if (!editableContent.value.sections.mission.stats) {
      editableContent.value.sections.mission.stats = [];
    }
    editableContent.value.sections.mission.stats.push({
      number: '0+',
      label: 'New Stat',
    });
  };

  const removeMissionStat = (index) => {
    if (editableContent.value.sections.mission.stats) {
      editableContent.value.sections.mission.stats.splice(index, 1);
    }
  };

  // Method to create new adventure
  const createNewAdventure = () => {
    // Redirect to adventure creation page or open modal
    window.location.href = '/dashboard/adventures/create';
  };

  // Method to edit adventure
  const editAdventure = (adventureId) => {
    if (props.isEditMode) {
      window.location.href = `/dashboard/adventures/${adventureId}/edit`;
    }
  };

  // Helper to check if currently editing
  const isEditing = (section, field = null, index = null) => {
    if (index !== null) {
      return editingSection.value === `${section}[${index}]` && editingField.value === field;
    }
    return editingSection.value === section && editingField.value === field;
  };

  // ✅ ADD: Apply theme colors when component mounts
  onMounted(() => {
    console.log('HomePage theme colors:', themeColors.value);
  });
</script>

<template>
  <div class="overflow-hidden relative theme-page" :class="{ 'editing-mode': isEditMode }">
    <!-- Edit Mode Indicator -->
    <div
      v-if="isEditMode"
      class="fixed top-4 right-4 z-50 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2"
    >
      <FontAwesomeIcon :icon="faEdit" />
      <span class="font-bold">Edit Mode</span>
    </div>

    <section class="relative theme-gradient-primary text-white py-16 md:py-24 overflow-hidden">
      <!-- Background Image with Dynamic Overlay -->
      <div class="absolute inset-0">
        <img
          :src="
            editableContent.sections?.hero?.backgroundImage ||
            defaultPageContent.sections.hero.backgroundImage
          "
          alt="Mountain landscape"
          class="w-full h-full object-cover scale-105 animate-slow-zoom"
        />
        <!-- Multi-layered gradient overlays for depth -->
        <div
          class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-purple-800/50 to-teal-900/75"
        ></div>
        <div
          class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
        ></div>
        <div
          class="absolute inset-0 bg-gradient-radial from-transparent via-transparent to-black/40"
        ></div>
      </div>

      <!-- Enhanced Animated Background Elements with Glow -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
          class="absolute top-16 -left-20 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl animate-float-glow"
        ></div>
        <div
          class="absolute top-32 right-10 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-3xl animate-pulse-glow-slow"
        ></div>
        <div
          class="absolute bottom-10 left-1/4 w-80 h-80 bg-amber-400/15 rounded-full blur-3xl animate-float-delayed"
        ></div>
        <div
          class="absolute top-1/2 right-1/3 w-64 h-64 bg-emerald-400/15 rounded-full blur-3xl animate-pulse-glow delay-1000"
        ></div>
      </div>

      <!-- Floating Particles Effect -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          class="absolute top-[10%] left-[15%] w-2 h-2 bg-white/40 rounded-full animate-float-particle"
        ></div>
        <div
          class="absolute top-[25%] left-[75%] w-1.5 h-1.5 bg-amber-300/50 rounded-full animate-float-particle delay-500"
        ></div>
        <div
          class="absolute top-[60%] left-[20%] w-2.5 h-2.5 bg-cyan-300/40 rounded-full animate-float-particle delay-1000"
        ></div>
        <div
          class="absolute top-[45%] left-[85%] w-1 h-1 bg-purple-300/50 rounded-full animate-float-particle delay-1500"
        ></div>
        <div
          class="absolute top-[80%] left-[50%] w-2 h-2 bg-emerald-300/40 rounded-full animate-float-particle delay-2000"
        ></div>
      </div>

      <!-- Floating Icons with Enhanced Animations -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-10 text-white/25 text-4xl animate-float-bounce">
          <FontAwesomeIcon :icon="faCompass" class="drop-shadow-glow" />
        </div>
        <div class="absolute top-1/3 right-16 text-white/20 text-5xl animate-float-spin-slow">
          <FontAwesomeIcon :icon="faMountain" class="drop-shadow-glow" />
        </div>
        <div class="absolute bottom-1/4 left-1/4 text-white/25 text-3xl animate-float-pulse">
          <FontAwesomeIcon :icon="faUsers" class="drop-shadow-glow" />
        </div>
        <div
          class="absolute top-1/2 right-1/4 text-white/15 text-6xl animate-float-drift delay-3000"
        >
          <FontAwesomeIcon :icon="faTree" class="drop-shadow-glow" />
        </div>
        <div
          class="absolute bottom-1/3 right-10 text-white/20 text-2xl animate-float-bounce-delayed"
        >
          <FontAwesomeIcon :icon="faCampground" class="drop-shadow-glow" />
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <!-- Premium Badge with Shine Effect -->
        <div
          class="mb-8 inline-block animate-slide-down relative"
          :class="{ 'editable-section': isEditMode }"
        >
          <span
            class="glass-effect-premium text-white px-8 py-3.5 rounded-full text-sm font-bold backdrop-blur-xl border-2 border-white/40 inline-flex items-center space-x-3 shadow-2xl hover:scale-105 transition-transform duration-300 cursor-pointer group"
            @click="isEditMode && startEditing('hero', 'badge')"
          >
            <FontAwesomeIcon :icon="faStar" class="text-yellow-400 animate-pulse-bright" />
            <span v-if="!isEditing('hero', 'badge')" class="relative">
              {{ editableContent.sections?.hero?.badge || defaultPageContent.sections.hero.badge }}
              <span
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shine"
              ></span>
            </span>
            <input
              v-else
              v-model="editableContent.sections.hero.badge"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 border-white outline-none text-center w-full"
              autofocus
            />
            <FontAwesomeIcon
              :icon="faArrowRight"
              class="text-xs group-hover:translate-x-1 transition-transform"
            />
          </span>
          <div v-if="isEditMode" class="edit-controls absolute -top-2 -right-2 flex space-x-1">
            <button
              class="bg-blue-500 text-white p-1.5 rounded-full text-xs hover:bg-blue-600 transition-colors shadow-lg"
            >
              <FontAwesomeIcon :icon="faEdit" />
            </button>
          </div>
        </div>

        <!-- Main Heading with Text Shadow and Glow -->
        <h1
          class="text-5xl md:text-6xl lg:text-7xl font-black mb-8 leading-tight animate-fade-scale-up relative"
          :class="{ 'editable-section': isEditMode }"
        >
          <span
            class="block mb-4 drop-shadow-2xl hover:scale-105 transition-transform duration-300 cursor-pointer"
            @click="isEditMode && startEditing('hero', 'title')"
            style="
              text-shadow:
                0 0 40px rgba(255, 255, 255, 0.3),
                0 4px 20px rgba(0, 0, 0, 0.8);
            "
          >
            <span
              v-if="!isEditing('hero', 'title')"
              class="bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent"
            >
              {{ editableContent.sections?.hero?.title || defaultPageContent.sections.hero.title }}
            </span>
            <input
              v-else
              v-model="editableContent.sections.hero.title"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 border-white outline-none text-center w-full"
              autofocus
            />
          </span>

          <!-- Hero Description Text -->
          <p
            class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto mb-8 animate-fade-in-up delay-200 font-light leading-relaxed cursor-pointer hover:opacity-100 transition-opacity"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('hero', 'text')"
            style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7)"
          >
            <span v-if="!isEditing('hero', 'text')">
              {{ editableContent.sections?.hero?.text || defaultPageContent.sections.hero.text }}
            </span>
            <textarea
              v-else
              v-model="editableContent.sections.hero.text"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 border-white outline-none text-center w-full resize-none"
              rows="2"
            />
          </p>

          <!-- Subtitle with Animated Gradient -->
          <p
            class="text-2xl md:text-3xl opacity-95 max-w-4xl mx-auto leading-relaxed font-semibold animate-fade-in-up delay-300 cursor-pointer group"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('hero', 'subtitle')"
          >
            <span
              v-if="!isEditing('hero', 'subtitle')"
              class="block bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 bg-clip-text text-transparent animate-gradient-x drop-shadow-lg group-hover:scale-105 transition-transform"
              style="background-size: 200% 200%"
            >
              {{
                editableContent.sections?.hero?.subtitle ||
                defaultPageContent.sections.hero.subtitle
              }}
            </span>
            <textarea
              v-else
              v-model="editableContent.sections.hero.subtitle"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 border-white outline-none text-center w-full resize-none"
              rows="3"
            />
          </p>
        </h1>

        <!-- Enhanced CTA Cards with Hover Effects -->
        <div
          class="mt-12 flex flex-col sm:flex-row justify-center gap-6 animate-fade-in-up delay-500"
        >
          <!-- First CTA Card -->
          <div
            class="group glass-effect-card rounded-2xl px-10 py-6 backdrop-blur-xl border-2 border-white/30 hover:border-amber-400/70 transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:scale-105 hover:-translate-y-1 shadow-2xl hover:shadow-amber-500/20"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('hero', 'cta1')"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-amber-400/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div class="flex items-center space-x-4 relative z-10">
              <div
                class="text-amber-400 text-3xl group-hover:rotate-12 group-hover:scale-125 transition-all duration-500"
              >
                <FontAwesomeIcon :icon="faCompass" class="drop-shadow-lg" />
              </div>
              <div>
                <div
                  class="text-white font-bold text-xl mb-1"
                  @click.stop="isEditMode && startEditing('hero', 'cta1Title')"
                >
                  <span v-if="!isEditing('hero', 'cta1Title')">
                    {{
                      editableContent.sections?.hero?.cta1Title ||
                      defaultPageContent.sections.hero.cta1Title
                    }}
                  </span>
                  <input
                    v-else
                    v-model="editableContent.sections.hero.cta1Title"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 border-white outline-none w-full"
                    autofocus
                  />
                </div>
                <div
                  class="text-white/80 text-sm font-medium"
                  @click.stop="isEditMode && startEditing('hero', 'cta1Subtitle')"
                >
                  <span v-if="!isEditing('hero', 'cta1Subtitle')">
                    {{
                      editableContent.sections?.hero?.cta1Subtitle ||
                      defaultPageContent.sections.hero.cta1Subtitle
                    }}
                  </span>
                  <input
                    v-else
                    v-model="editableContent.sections.hero.cta1Subtitle"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 border-white outline-none w-full"
                    autofocus
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Second CTA Card -->
          <div
            class="group glass-effect-card rounded-2xl px-10 py-6 backdrop-blur-xl border-2 border-white/30 hover:border-emerald-400/70 transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:scale-105 hover:-translate-y-1 shadow-2xl hover:shadow-emerald-500/20"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('hero', 'cta2')"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-emerald-400/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div class="flex items-center space-x-4 relative z-10">
              <div
                class="text-emerald-400 text-3xl group-hover:scale-125 transition-all duration-500"
              >
                <FontAwesomeIcon :icon="faPlayCircle" class="drop-shadow-lg" />
              </div>
              <div>
                <div
                  class="text-white font-bold text-xl mb-1"
                  @click.stop="isEditMode && startEditing('hero', 'cta2Title')"
                >
                  <span v-if="!isEditing('hero', 'cta2Title')">
                    {{
                      editableContent.sections?.hero?.cta2Title ||
                      defaultPageContent.sections.hero.cta2Title
                    }}
                  </span>
                  <input
                    v-else
                    v-model="editableContent.sections.hero.cta2Title"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 border-white outline-none w-full"
                    autofocus
                  />
                </div>
                <div
                  class="text-white/80 text-sm font-medium"
                  @click.stop="isEditMode && startEditing('hero', 'cta2Subtitle')"
                >
                  <span v-if="!isEditing('hero', 'cta2Subtitle')">
                    {{
                      editableContent.sections?.hero?.cta2Subtitle ||
                      defaultPageContent.sections.hero.cta2Subtitle
                    }}
                  </span>
                  <input
                    v-else
                    v-model="editableContent.sections.hero.cta2Subtitle"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 border-white outline-none w-full"
                    autofocus
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Background Image Editor (Edit Mode Only) -->
        <div v-if="isEditMode" class="mt-10 max-w-2xl mx-auto">
          <div class="glass-effect-card rounded-xl p-6 border border-white/20">
            <h3 class="text-white font-bold mb-3 text-lg">🖼️ Hero Background Image</h3>
            <div class="flex space-x-2">
              <input
                v-model="editableContent.sections.hero.backgroundImage"
                placeholder="Enter image URL..."
                class="flex-1 bg-white/10 border border-white/30 rounded-lg px-4 py-3 text-white placeholder-white/50 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all"
              />
              <button
                @click="
                  editableContent.sections.hero.backgroundImage =
                    defaultPageContent.sections.hero.backgroundImage
                "
                class="bg-red-500 text-white px-5 py-3 rounded-lg hover:bg-red-600 transition-colors font-semibold shadow-lg"
              >
                Reset
              </button>
            </div>
            <p class="text-white/60 text-sm mt-3">
              💡 Paste a direct image URL. Recommended: 1920x1080px or larger for best quality.
            </p>
          </div>
        </div>

        <!-- Enhanced Stats Section with Animated Cards -->
        <div
          class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto animate-fade-in-up delay-700"
        >
          <div
            v-for="(stat, index) in editableContent.sections?.hero?.stats ||
            defaultPageContent.sections.hero.stats"
            :key="index"
            class="stat-card-premium glass-effect-card rounded-2xl p-6 text-center relative overflow-hidden group cursor-pointer transform hover:scale-105 transition-all duration-500 border-2 border-white/20 hover:border-amber-300/50 shadow-xl hover:shadow-2xl"
            :class="{ 'editable-section': isEditMode }"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div
              class="text-4xl md:text-5xl font-black text-white mb-3 flex justify-center relative z-10"
              @click="isEditMode && startEditing('heroStats', 'number', index)"
            >
              <span
                v-if="!isEditing('heroStats', 'number', index)"
                class="bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 bg-clip-text text-transparent drop-shadow-lg animate-pulse-subtle"
              >
                {{ stat.number }}
              </span>
              <input
                v-else
                v-model="stat.number"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 border-white outline-none text-center w-24"
                autofocus
              />
            </div>
            <div
              class="text-white font-bold text-lg mb-2 relative z-10"
              @click="isEditMode && startEditing('heroStats', 'label', index)"
            >
              <span v-if="!isEditing('heroStats', 'label', index)">{{ stat.label }}</span>
              <input
                v-else
                v-model="stat.label"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 border-white outline-none text-center w-full"
                autofocus
              />
            </div>
            <div
              class="text-white/70 text-sm relative z-10"
              @click="isEditMode && startEditing('heroStats', 'description', index)"
            >
              <span v-if="!isEditing('heroStats', 'description', index)">
                {{ stat.description || 'By our community of explorers' }}
              </span>
              <input
                v-else
                v-model="stat.description"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 border-white outline-none text-center w-full text-sm"
                autofocus
              />
            </div>
            <div
              v-if="isEditMode"
              class="edit-controls absolute -top-3 -right-3 flex space-x-1 z-20"
            >
              <button
                @click="removeHeroStat(index)"
                class="bg-red-500 text-white p-2 rounded-full text-xs hover:bg-red-600 transition-colors shadow-lg"
              >
                <FontAwesomeIcon :icon="faTimes" />
              </button>
            </div>
          </div>

          <!-- Add Stat Button -->
          <div v-if="isEditMode" class="col-span-3 flex justify-center mt-4">
            <button
              @click="addHeroStat"
              class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-xl flex items-center space-x-2 hover:from-green-600 hover:to-emerald-600 transition-all duration-300 font-bold shadow-xl hover:shadow-2xl transform hover:scale-105"
            >
              <FontAwesomeIcon :icon="faPlus" />
              <span>Add New Stat</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 theme-bg-accent relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-[0.02]">
        <div
          class="absolute inset-0"
          style="
            background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80');
          "
        ></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Content -->
          <div class="animate-fade-in-up">
            <div class="inline-block mb-6 relative" :class="{ 'editable-section': isEditMode }">
              <span
                class="theme-bg-primary/10 theme-text-primary px-5 py-2 rounded-full text-sm font-bold flex items-center space-x-2"
                @click="isEditMode && startEditing('missionBadge')"
              >
                <FontAwesomeIcon :icon="faGlobe" />
                <span>MY MISSION And Goal</span>
              </span>
            </div>

            <h2
              class="text-4xl md:text-5xl lg:text-6xl font-black theme-text-primary mb-6 leading-tight relative"
              :class="{ 'editable-section': isEditMode }"
              @click="isEditMode && startEditing('mission', 'title')"
            >
              <span v-if="!isEditing('mission', 'title')">
                {{
                  editableContent.sections?.mission?.title ||
                  defaultPageContent.sections.mission.title
                }}
              </span>
              <input
                v-else
                v-model="editableContent.sections.mission.title"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 theme-border-primary outline-none w-full"
                autofocus
              />
            </h2>

            <p
              class="text-xl theme-text-secondary mb-8 leading-relaxed relative"
              :class="{ 'editable-section': isEditMode }"
              @click="isEditMode && startEditing('mission', 'content')"
            >
              <span v-if="!isEditing('mission', 'content')">
                {{
                  editableContent.sections?.mission?.content ||
                  defaultPageContent.sections.mission.content
                }}
              </span>
              <textarea
                v-else
                v-model="editableContent.sections.mission.content"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 outline-none w-full resize-none"
                rows="4"
                autofocus
              />
            </p>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 gap-6 animate-fade-in-up delay-200">
            <div
              v-for="(stat, index) in editableContent.sections?.mission?.stats ||
              defaultPageContent.sections.mission.stats"
              :key="index"
              class="group theme-bg-accent p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 theme-border-primary relative"
              :class="{ 'editable-section': isEditMode }"
            >
              <div class="text-center">
                <div
                  class="text-3xl md:text-4xl font-black theme-text-primary mb-2 group-hover:theme-text-secondary transition-colors"
                  @click="isEditMode && startEditing('missionStats', 'number', index)"
                >
                  <span v-if="!isEditing('missionStats', 'number', index)">{{ stat.number }}</span>
                  <input
                    v-else
                    v-model="stat.number"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 theme-border-primary outline-none text-center w-20"
                    autofocus
                  />
                </div>
                <div
                  class="theme-text-secondary font-medium"
                  @click="isEditMode && startEditing('missionStats', 'label', index)"
                >
                  <span v-if="!isEditing('missionStats', 'label', index)">{{ stat.label }}</span>
                  <input
                    v-else
                    v-model="stat.label"
                    @blur="stopEditing"
                    @keyup.enter="stopEditing"
                    class="bg-transparent border-b-2 theme-border-secondary outline-none text-center w-full"
                    autofocus
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Grid with Modern Cards -->
    <section class="py-20 relative">
      <!-- Decorative Elements -->
      <div class="absolute top-0 left-0 w-full h-1"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <div class="inline-block mb-6 relative" :class="{ 'editable-section': isEditMode }">
            <span
              class="theme-bg-primary/10 theme-text-primary px-5 py-2 rounded-full text-sm font-bold"
              @click="isEditMode && startEditing('featuresBadge')"
            >
              ✨ FEATURES
            </span>
          </div>

          <h2
            class="text-5xl md:text-6xl font-black theme-text-primary mb-8 leading-tight relative"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('features', 'title')"
          >
            <span v-if="!isEditing('features', 'title')">
              {{
                editableContent.sections?.features?.title ||
                defaultPageContent.sections.features.title
              }}
            </span>
            <input
              v-else
              v-model="editableContent.sections.features.title"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 theme-border-primary outline-none text-center w-full"
              autofocus
            />
          </h2>

          <p class="text-xl theme-text-secondary max-w-3xl mx-auto leading-relaxed">
            Everything you need to document and share your amazing adventures with the world
          </p>
        </div>

        <!-- Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="(feature, index) in editableContent.sections?.features?.items ||
            defaultPageContent.sections.features.items"
            :key="index"
            class="group relative theme-bg-accent p-8 rounded-3xl border-2 theme-border-secondary hover:theme-border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
            :class="{ 'editable-section': isEditMode }"
          >
            <!-- Icon with Gradient Background -->
            <div class="mb-6 inline-flex">
              <div class="relative">
                <div
                  class="absolute inset-0 theme-gradient-primary rounded-2xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity"
                ></div>
                <div
                  class="relative theme-gradient-primary theme-text-accent p-5 rounded-2xl text-3xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300"
                >
                  <!-- Use editable icon -->
                  <span v-if="feature.icon">{{ feature.icon }}</span>
                  <FontAwesomeIcon
                    v-else
                    :icon="index === 0 ? faMapMarkedAlt : index === 1 ? faCamera : faUsers"
                  />
                </div>
              </div>
            </div>

            <h3
              class="text-2xl font-bold theme-text-primary mb-4 group-hover:theme-text-secondary transition-colors relative"
              @click="isEditMode && startEditing('featureItem', 'title', index)"
            >
              <span v-if="!isEditing('featureItem', 'title', index)">{{ feature.title }}</span>
              <input
                v-else
                v-model="feature.title"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 theme-border-primary outline-none w-full"
                autofocus
              />
            </h3>

            <p
              class="theme-text-secondary text-lg leading-relaxed mb-6 relative"
              @click="isEditMode && startEditing('featureItem', 'description', index)"
            >
              <span v-if="!isEditing('featureItem', 'description', index)">
                {{ feature.description }}
              </span>
              <textarea
                v-else
                v-model="feature.description"
                @blur="stopEditing"
                @keyup.enter="stopEditing"
                class="bg-transparent border-b-2 theme-border-secondary outline-none w-full resize-none"
                rows="3"
                autofocus
              />
            </p>

            <!-- Learn More Link -->
            <div
              class="flex items-center theme-text-primary font-semibold group-hover:theme-text-secondary transition-colors"
            >
              <span class="mr-2">Learn more</span>
              <FontAwesomeIcon
                :icon="faArrowRight"
                class="transform group-hover:translate-x-2 transition-transform"
              />
            </div>

            <!-- Edit Controls -->
            <div v-if="isEditMode" class="edit-controls absolute -top-2 -right-2 flex space-x-1">
              <button
                @click="removeFeatureItem(index)"
                class="bg-red-500 text-white p-1 rounded-full text-xs"
              >
                <FontAwesomeIcon :icon="faTimes" />
              </button>
            </div>

            <!-- Decorative Corner -->
            <div
              class="absolute top-0 right-0 w-20 h-20 theme-bg-primary/5 rounded-bl-3xl rounded-tr-3xl"
            ></div>
          </div>

          <!-- Add Feature Button -->
          <div v-if="isEditMode" class="flex items-center justify-center">
            <button
              @click="addFeatureItem"
              class="bg-green-500 text-white p-4 rounded-3xl border-2 border-dashed border-green-300 hover:bg-green-600 transition-all duration-300 flex flex-col items-center justify-center w-full h-full min-h-[300px]"
            >
              <FontAwesomeIcon :icon="faPlus" class="text-2xl mb-2" />
              <span class="font-semibold">Add New Feature</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Recent Adventures Section -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white relative overflow-hidden">
      <!-- Subtle Background Pattern -->
      <div class="absolute inset-0 opacity-[0.03]">
        <div
          class="absolute inset-0"
          style="
            background-image: radial-gradient(circle at 2px 2px, #000000 1px, transparent 0);
            background-size: 32px 32px;
          "
        ></div>
      </div>

      <!-- Decorative Elements -->
      <div
        class="absolute top-0 right-0 w-96 h-96 bg-black/5 rounded-full blur-3xl -mr-48 -mt-48"
      ></div>
      <div
        class="absolute bottom-0 left-0 w-96 h-96 bg-black/5 rounded-full blur-3xl -ml-48 -mb-48"
      ></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
          <!-- Badge -->
          <div class="inline-block mb-6" :class="{ 'editable-section': isEditMode }">
            <span
              class="inline-flex items-center space-x-2 bg-black text-white px-6 py-2.5 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
              @click="isEditMode && startEditing('recentBadge')"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                />
              </svg>
              <span>Recent Adventures</span>
            </span>
          </div>

          <!-- Main Title -->
          <h2
            class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-black mb-6 leading-tight relative"
            :class="{ 'editable-section': isEditMode }"
            @click="isEditMode && startEditing('recent', 'title')"
          >
            <span v-if="!isEditing('recent', 'title')" class="relative inline-block">
              {{
                editableContent.sections?.recent?.title || defaultPageContent.sections.recent.title
              }}
              <!-- Underline accent -->
              <span
                class="absolute -bottom-2 left-0 w-full h-1 bg-black transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"
              ></span>
            </span>
            <input
              v-else
              v-model="editableContent.sections.recent.title"
              @blur="stopEditing"
              @keyup.enter="stopEditing"
              class="bg-transparent border-b-2 border-black outline-none text-center w-full"
              autofocus
            />
          </h2>

          <!-- Subtitle -->
          <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed font-light">
            Explore my latest journeys and experiences from around the world
          </p>

          <!-- Adventure Count -->
          <div class="mt-4 text-sm text-gray-500 font-medium">
            Showing {{ displayedAdventures.length }} of {{ totalAdventures }} adventures
          </div>

          <!-- Divider -->
          <div class="flex items-center justify-center mt-8 space-x-4">
            <div class="w-12 h-px bg-black"></div>
            <div class="w-2 h-2 bg-black rotate-45"></div>
            <div class="w-12 h-px bg-black"></div>
          </div>
        </div>

        <!-- Adventure Cards Grid -->
        <div class="mb-12 sm:mb-16">
          <!-- Show Adventure Cards when there ARE adventures -->
          <div v-if="hasAdventures" class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
            <div
              v-for="(post, index) in displayedAdventures"
              :key="post.id || index"
              class="group bg-white border-2 border-black rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer"
              @click="editAdventure(post.id)"
            >
              <!-- Image Container -->
              <div class="relative h-72 sm:h-80 lg:h-96 overflow-hidden bg-black">
                <img
                  :src="post.image"
                  :alt="post.title"
                  class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700"
                />

                <!-- Gradient Overlay -->
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
                ></div>

                <!-- Date Badge - Top Right -->
                <div
                  class="absolute top-4 right-4 bg-white text-black px-4 py-2 rounded-lg font-bold text-sm shadow-lg"
                >
                  {{
                    new Date(post.date).toLocaleDateString('en-US', {
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric',
                    })
                  }}
                </div>

                <!-- Location Badge - Bottom Left (if exists) -->
                <div
                  v-if="post.location"
                  class="absolute bottom-4 left-4 bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm font-medium border border-white/20"
                >
                  📍 {{ post.location }}
                </div>
              </div>

              <!-- Content Container -->
              <div class="p-6 sm:p-8 bg-white">
                <!-- Title -->
                <h3
                  class="text-2xl sm:text-3xl font-black text-black mb-4 leading-tight group-hover:text-gray-700 transition-colors line-clamp-2"
                >
                  {{ post.title }}
                </h3>

                <!-- Excerpt -->
                <p class="text-gray-600 text-base sm:text-lg leading-relaxed mb-6 line-clamp-3">
                  {{ post.excerpt }}
                </p>

                <!-- Read More Link -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                  <span
                    class="text-sm font-bold text-black uppercase tracking-wider group-hover:translate-x-2 transition-transform"
                  >
                    Read Story
                  </span>
                  <svg
                    class="w-6 h-6 text-black group-hover:translate-x-2 transition-transform"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 8l4 4m0 0l-4 4m4-4H3"
                    />
                  </svg>
                </div>
              </div>

              <!-- Number Badge -->
              <div
                class="absolute top-0 left-0 w-16 h-16 bg-black text-white flex items-center justify-center font-black text-2xl"
              >
                {{ String(index + 1).padStart(2, '0') }}
              </div>
            </div>
          </div>

          <!-- Show Empty State when there are NO adventures -->
          <div v-else class="text-center py-16 sm:py-20 lg:py-24">
            <!-- Icon -->
            <div class="mb-8 inline-block">
              <div
                class="w-32 h-32 sm:w-40 sm:h-40 bg-black/5 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-16 h-16 sm:w-20 sm:h-20 text-black/20"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <!-- Title -->
            <h3 class="text-3xl sm:text-4xl font-black text-black mb-4">No Adventures Yet</h3>

            <!-- Description -->
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-8 leading-relaxed">
              Start documenting your journeys to see them here. Share your stories, photos, and
              experiences with the world.
            </p>

            <!-- CTA Button -->
            <button
              @click="createNewAdventure"
              class="group inline-flex items-center space-x-3 bg-black text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-900 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              <span>Create Your First Adventure</span>
            </button>
          </div>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMoreAdventures" class="text-center mb-12 sm:mb-16">
          <button
            @click="loadMoreAdventures"
            :disabled="loadingMore"
            class="group inline-flex items-center space-x-3 bg-transparent border-2 border-black text-black px-8 py-4 rounded-xl font-bold text-lg hover:bg-black hover:text-white transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg
              v-if="loadingMore"
              class="w-6 h-6 animate-spin"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
            <svg
              v-else
              class="w-6 h-6 group-hover:translate-y-1 transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 14l-7 7m0 0l-7-7m7 7V3"
              />
            </svg>
            <span>
              {{
                loadingMore
                  ? 'Loading...'
                  : `Load More (${totalAdventures - displayedAdventures.length} remaining)`
              }}
            </span>
          </button>

          <!-- Progress indicator -->
          <div class="mt-4 max-w-md mx-auto">
            <div class="bg-gray-200 rounded-full h-2">
              <div
                class="bg-black h-2 rounded-full transition-all duration-500"
                :style="{ width: `${(displayedAdventures.length / totalAdventures) * 100}%` }"
              ></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
              {{ displayedAdventures.length }} of {{ totalAdventures }} adventures shown
            </p>
          </div>
        </div>

        <!-- CTA Section (shown when there ARE adventures) -->
        <div v-if="hasAdventures && !hasMoreAdventures" class="relative mt-8">
          <!-- Background -->
          <div class="absolute inset-0 bg-black rounded-3xl"></div>

          <!-- Pattern Overlay -->
          <div
            class="absolute inset-0 opacity-10 rounded-3xl"
            style="
              background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
              background-size: 24px 24px;
            "
          ></div>

          <!-- Content -->
          <div class="relative p-8 sm:p-12 lg:p-16 text-center">
            <!-- Icon -->
            <div class="inline-block mb-6">
              <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-black" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <!-- Title -->
            <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6 leading-tight">
              All Adventures Loaded!
            </h3>

            <!-- Description -->
            <p class="text-lg sm:text-xl text-white/80 max-w-2xl mx-auto mb-8 leading-relaxed">
              You've explored all {{ totalAdventures }} adventures. Ready to create your next
              journey?
            </p>

            <!-- CTA Button -->
            <button
              @click="createNewAdventure"
              class="group inline-flex items-center space-x-3 bg-white text-black px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105"
            >
              <svg
                class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              <span>Add New Adventure</span>
            </button>

            <!-- Stats -->
            <div class="mt-12 pt-12 border-t border-white/20">
              <div class="grid grid-cols-3 gap-8 max-w-2xl mx-auto">
                <div class="text-center">
                  <div class="text-3xl sm:text-4xl font-black text-white mb-2">
                    {{ totalAdventures }}+
                  </div>
                  <div class="text-sm text-white/60 uppercase tracking-wider">Adventures</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl sm:text-4xl font-black text-white mb-2">
                    {{
                      new Set(
                        editableContent.sections?.recent?.posts
                          ?.map((p) => p.location)
                          .filter(Boolean)
                      ).size || 0
                    }}+
                  </div>
                  <div class="text-sm text-white/60 uppercase tracking-wider">Locations</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl sm:text-4xl font-black text-white mb-2">
                    {{ new Date().getFullYear() }}
                  </div>
                  <div class="text-sm text-white/60 uppercase tracking-wider">Year</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Save Button (only in edit mode) -->
    <div v-if="isEditMode" class="fixed bottom-4 right-4 z-50">
      <button
        @click="saveChanges"
        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-300"
      >
        <FontAwesomeIcon :icon="faSave" />
        <span>Save Changes</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
  /* Animations */
  @keyframes fade-in-down {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fade-in-up {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes float-delayed {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }
    50% {
      transform: translateY(-30px) rotate(5deg);
    }
  }

  @keyframes float-slow {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-15px);
    }
  }

  .animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out;
  }

  .animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out;
  }

  .animate-fade-in-up.delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-fade-in-up.delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-fade-in-up.delay-600 {
    animation-delay: 0.6s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-float-delayed {
    animation: float-delayed 7s ease-in-out infinite;
  }

  .animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
  }

  .animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  .delay-1000 {
    animation-delay: 1s;
  }

  .delay-2000 {
    animation-delay: 2s;
  }

  /* Edit Mode Styles */
  .editing-mode .editable-section {
    position: relative;
    transition: all 0.3s ease;
  }

  .editing-mode .editable-section:hover {
    background-color: rgba(59, 130, 246, 0.1);
    outline: 2px dashed #3b82f6;
    outline-offset: 2px;
    border-radius: 4px;
    cursor: pointer;
  }

  .edit-controls {
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .editable-section:hover .edit-controls {
    opacity: 1;
  }

  /* Shadow Variations */
  .shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }

  /* Input styles for editing */
  input,
  textarea {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
  }

  input:focus,
  textarea:focus {
    background: rgba(255, 255, 255, 0.2);
  }

  /* Theme CSS Classes - Add these */
  .theme-gradient-primary {
    background: linear-gradient(
      135deg,
      var(--color-primary, #000000),
      var(--color-secondary, #8b4513)
    );
  }

  .theme-bg-primary {
    background-color: var(--color-primary, #000000);
  }

  .theme-bg-accent {
    background-color: var(--color-accent, #ffffff);
  }

  .theme-text-primary {
    color: var(--color-primary, #000000);
  }

  .theme-text-secondary {
    color: var(--color-secondary, #8b4513);
  }

  .theme-text-accent {
    color: var(--color-accent, #ffffff);
  }

  .theme-border-primary {
    border-color: var(--color-primary, #000000);
  }

  .theme-border-secondary {
    border-color: var(--color-secondary, #8b4513);
  }

  /* Ensure all color classes use CSS variables */
  .text-primary {
    color: var(--color-primary, #000000) !important;
  }

  .bg-primary {
    background-color: var(--color-primary, #000000) !important;
  }

  .border-primary {
    border-color: var(--color-primary, #000000) !important;
  }

  .text-secondary {
    color: var(--color-secondary, #8b4513) !important;
  }

  .bg-secondary {
    background-color: var(--color-secondary, #8b4513) !important;
  }

  .border-secondary {
    border-color: var(--color-secondary, #8b4513) !important;
  }

  .text-accent {
    color: var(--color-accent, #ffffff) !important;
  }

  .bg-accent {
    background-color: var(--color-accent, #ffffff) !important;
  }

  .border-accent {
    border-color: var(--color-accent, #ffffff) !important;
  }

  /* Line clamp utilities */
  .line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  .line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
  }
</style>
