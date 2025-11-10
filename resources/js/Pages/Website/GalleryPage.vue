<script setup>
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faCamera,
    faMountain,
    faMap,
    faStar,
    faPlus,
    faUpload,
    faEye,
    faImages,
    faMapMarkerAlt,
    faTimes,
    faArrowLeft,
    faArrowRight,
    faTrash,
    faCompass,
  } from '@fortawesome/free-solid-svg-icons';
  import { ref, onMounted, onUnmounted, computed } from 'vue';

  // Define props and make them reactive
  const props = defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean,
  });

  const emit = defineEmits(['deleteImage', 'openUpload']);

  // Pagination state
  const itemsPerPage = ref(9);
  const currentPage = ref(1);

  // Computed property for displayed images
  const displayedImages = computed(() => {
    const allImages = props.pageContent?.images || [];
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return allImages.slice(0, startIndex + itemsPerPage.value);
  });

  // Check if there are more images to load
  const hasMoreImages = computed(() => {
    const allImages = props.pageContent?.images || [];
    return displayedImages.value.length < allImages.length;
  });

  // Load more images
  const loadMore = () => {
    currentPage.value += 1;
  };

  // Reset pagination when images change
  const resetPagination = () => {
    currentPage.value = 1;
  };

  // Image preview modal state
  const showImagePreview = ref(false);
  const selectedImage = ref(null);
  const currentImageIndex = ref(0);

  // Open image preview
  const openImagePreview = (image, index) => {
    selectedImage.value = image;
    currentImageIndex.value = index;
    showImagePreview.value = true;
  };

  // Close image preview
  const closeImagePreview = () => {
    showImagePreview.value = false;
    selectedImage.value = null;
    currentImageIndex.value = 0;
  };

  // Navigate between images - FIXED: use props.pageContent
  const nextImage = () => {
    const images = props.pageContent?.images || [];
    if (images.length === 0) return;

    currentImageIndex.value = (currentImageIndex.value + 1) % images.length;
    selectedImage.value = images[currentImageIndex.value];
  };

  const prevImage = () => {
    const images = props.pageContent?.images || [];
    if (images.length === 0) return;

    currentImageIndex.value = (currentImageIndex.value - 1 + images.length) % images.length;
    selectedImage.value = images[currentImageIndex.value];
  };

  // Keyboard navigation
  const handleKeydown = (event) => {
    if (!showImagePreview.value) return;

    switch (event.key) {
      case 'Escape':
        closeImagePreview();
        break;
      case 'ArrowLeft':
        prevImage();
        break;
      case 'ArrowRight':
        nextImage();
        break;
    }
  };

  // Add event listener for keyboard
  onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
  });

  // Remove event listener when component unmounts
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
  });

  // Add computed property to simplify access
  const heroSection = computed(() => {
    return props.pageContent?.sections?.hero || {};
  });
</script>

<template>
  <div class="animate-fade-in-up theme-page">
    <!-- ========== GALLERY PAGE HERO SECTION ========== -->

    <section
      class="bg-gradient-to-br from-primary to-secondary text-accent py-16 md:py-24 relative overflow-hidden"
    >
      <!-- Background with Dynamic Image -->
      <div class="absolute inset-0">
        <div
          v-if="heroSection.backgroundImage"
          class="absolute inset-0 bg-cover bg-center scale-105 animate-slow-zoom"
          :style="`background-image: url('${heroSection.backgroundImage}')`"
        ></div>

        <!-- Multi-layered Enhanced Gradient Overlay -->
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

      <!-- Enhanced Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
          class="absolute top-16 left-20 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float-glow"
        ></div>
        <div
          class="absolute bottom-20 right-32 w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-3xl animate-pulse-glow-slow"
        ></div>
        <div
          class="absolute top-1/2 left-1/4 w-80 h-80 bg-amber-400/15 rounded-full blur-3xl animate-float-delayed delay-2000"
        ></div>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          class="absolute top-[15%] left-[20%] w-2 h-2 bg-white/40 rounded-full animate-float-particle"
        ></div>
        <div
          class="absolute top-[30%] left-[70%] w-1.5 h-1.5 bg-purple-300/50 rounded-full animate-float-particle delay-500"
        ></div>
        <div
          class="absolute top-[55%] left-[25%] w-2.5 h-2.5 bg-cyan-300/40 rounded-full animate-float-particle delay-1000"
        ></div>
        <div
          class="absolute top-[40%] left-[80%] w-1 h-1 bg-amber-300/50 rounded-full animate-float-particle delay-1500"
        ></div>
      </div>

      <!-- Floating Icons with Enhanced Animations -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/3 left-16 text-white/25 text-5xl animate-float-bounce">
          <FontAwesomeIcon :icon="faCamera" class="drop-shadow-glow" />
        </div>
        <div class="absolute bottom-1/4 right-20 text-white/30 text-4xl animate-float-spin-slow">
          <FontAwesomeIcon :icon="faImages" class="drop-shadow-glow" />
        </div>
        <div class="absolute top-1/2 right-1/3 text-white/20 text-6xl animate-float-drift">
          <FontAwesomeIcon :icon="faMountain" class="drop-shadow-glow" />
        </div>
        <div
          class="absolute top-1/4 right-1/4 text-white/20 text-3xl animate-float-pulse delay-1500"
        >
          <FontAwesomeIcon :icon="faMap" class="drop-shadow-glow" />
        </div>
      </div>

      <!-- Foreground Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <!-- Premium Badge -->
        <div v-if="heroSection.badge" class="mb-8 inline-block animate-slide-down">
          <span
            class="glass-effect-premium text-white px-8 py-3.5 rounded-full text-sm font-bold backdrop-blur-xl border-2 border-white/40 inline-flex items-center space-x-3 shadow-2xl hover:scale-105 transition-transform duration-300 cursor-pointer group"
          >
            <FontAwesomeIcon :icon="faStar" class="text-yellow-400 animate-pulse-bright" />
            <span class="relative">
              {{ heroSection.badge }}
              <span
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shine"
              ></span>
            </span>
          </span>
        </div>

        <!-- Enhanced Heading -->
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-8 animate-fade-scale-up">
          <span
            class="block text-white drop-shadow-2xl mb-4"
            style="
              text-shadow:
                0 0 40px rgba(255, 255, 255, 0.3),
                0 4px 20px rgba(0, 0, 0, 0.8);
            "
          >
            {{ heroSection.title || 'Adventure Gallery' }}
          </span>
          <span
            class="block bg-gradient-to-r from-amber-300 via-yellow-400 to-emerald-400 bg-clip-text text-transparent animate-gradient-x drop-shadow-lg text-4xl md:text-5xl"
            style="background-size: 200% 200%"
          >
            {{ heroSection.subtitle || 'Visual stories from incredible journeys around the world' }}
          </span>
        </h1>

        <!-- Enhanced Gallery Stats - Fully Editable -->
        <div
          class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto mb-12 animate-fade-in-up delay-300"
        >
          <!-- Photos Shared -->
          <div
            class="glass-effect-card rounded-xl p-5 text-center group hover:scale-105 transition-all duration-300 border-2 border-white/20 hover:border-amber-300/50 shadow-lg"
          >
            <div
              class="text-3xl font-bold text-amber-400 mb-2 group-hover:scale-110 transition-transform"
            >
              {{ heroSection.photosShared || pageContent?.images?.length || 0 }}
            </div>
            <div class="text-white/90 text-sm font-semibold">
              {{ heroSection.photosSharedLabel || 'Photos Shared' }}
            </div>
          </div>

          <!-- Adventures Documented -->
          <div
            class="glass-effect-card rounded-xl p-5 text-center group hover:scale-105 transition-all duration-300 border-2 border-white/20 hover:border-emerald-300/50 shadow-lg"
          >
            <div
              class="text-3xl font-bold text-emerald-400 mb-2 group-hover:scale-110 transition-transform"
            >
              {{
                heroSection.adventuresDocumented ||
                Math.ceil((pageContent?.images?.length || 0) / 3) + '+'
              }}
            </div>
            <div class="text-white/90 text-sm font-semibold">
              {{ heroSection.adventuresDocumentedLabel || 'Adventures Documented' }}
            </div>
          </div>

          <!-- Countries Covered -->
          <div
            class="glass-effect-card rounded-xl p-5 text-center group hover:scale-105 transition-all duration-300 border-2 border-white/20 hover:border-blue-300/50 shadow-lg"
          >
            <div
              class="text-3xl font-bold text-blue-400 mb-2 group-hover:scale-110 transition-transform"
            >
              {{ heroSection.countriesCovered || '50+' }}
            </div>
            <div class="text-white/90 text-sm font-semibold">
              {{ heroSection.countriesCoveredLabel || 'Countries Covered' }}
            </div>
          </div>

          <!-- Community Active -->
          <div
            class="glass-effect-card rounded-xl p-5 text-center group hover:scale-105 transition-all duration-300 border-2 border-white/20 hover:border-purple-300/50 shadow-lg"
          >
            <div
              class="text-3xl font-bold text-purple-400 mb-2 group-hover:scale-110 transition-transform"
            >
              {{ heroSection.communityActive || '24/7' }}
            </div>
            <div class="text-white/90 text-sm font-semibold">
              {{ heroSection.communityActiveLabel || 'Community Active' }}
            </div>
          </div>
        </div>

        <!-- Enhanced Categories -->
        <div
          v-if="heroSection.categories"
          class="flex flex-wrap justify-center gap-4 mb-12 animate-fade-in-up delay-500"
        >
          <span
            v-for="(category, index) in heroSection.categories"
            :key="index"
            class="glass-effect-card text-white px-5 py-2.5 rounded-full text-sm border-2 border-white/30 hover:border-amber-400/60 transition-all duration-300 cursor-pointer hover:scale-105 font-semibold shadow-lg"
          >
            {{ category }}
          </span>
        </div>

        <!-- Enhanced CTA Buttons -->
        <div
          v-if="heroSection.cta1Title || heroSection.cta2Title"
          class="flex flex-col sm:flex-row justify-center items-center gap-6 animate-fade-in-up delay-700"
        >
          <div
            v-if="heroSection.cta1Title"
            class="group glass-effect-card rounded-2xl px-10 py-6 backdrop-blur-xl border-2 border-white/30 hover:border-amber-400/70 transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:scale-105 hover:-translate-y-1 shadow-2xl hover:shadow-amber-500/20"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-amber-400/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div class="flex items-center space-x-4 relative z-10">
              <div
                class="text-amber-400 text-3xl group-hover:rotate-12 group-hover:scale-125 transition-all duration-500"
              >
                <FontAwesomeIcon :icon="faCamera" class="drop-shadow-lg" />
              </div>
              <div>
                <div class="text-white font-bold text-xl mb-1">{{ heroSection.cta1Title }}</div>
                <div class="text-white/80 text-sm font-medium">{{ heroSection.cta1Subtitle }}</div>
              </div>
              <FontAwesomeIcon
                :icon="faArrowRight"
                class="text-white/60 group-hover:text-amber-400 group-hover:translate-x-2 transition-all duration-300 text-lg"
              />
            </div>
          </div>

          <div
            class="group glass-effect-card rounded-2xl px-10 py-6 backdrop-blur-xl border-2 border-white/30 hover:border-emerald-400/70 transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:scale-105 hover:-translate-y-1 shadow-2xl hover:shadow-emerald-500/20"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-emerald-400/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div class="flex items-center space-x-4 relative z-10">
              <div
                class="text-emerald-400 text-3xl group-hover:scale-125 transition-all duration-500"
              >
                <FontAwesomeIcon :icon="faUpload" class="drop-shadow-lg" />
              </div>
              <div>
                <div class="text-white font-bold text-xl mb-1">{{ heroSection.cta2Title }}</div>
                <div class="text-white/80 text-sm font-medium">{{ heroSection.cta2Subtitle }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Gallery Grid -->
    <section class="py-16 lg:py-20 bg-accent">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Image Counter - UPDATED -->
        <div class="text-center mb-8">
          <p class="text-gray-600 dark:text-gray-400">
            Showing {{ displayedImages.length }} of {{ pageContent?.images?.length || 0 }} photos
          </p>
        </div>

        <!-- Image Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
          <div
            v-for="(image, index) in displayedImages"
            :key="index"
            class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2"
          >
            <!-- Actual Image -->
            <div class="aspect-[4/3] bg-gray-200 relative overflow-hidden">
              <img
                :src="image.url"
                :alt="image.caption || `Adventure ${index + 1}`"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              />
              <!-- Overlay -->
              <div
                class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center"
              >
                <div
                  class="transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 flex space-x-3"
                >
                  <button
                    class="bg-accent text-black px-4 py-2 rounded-xl font-semibold hover:scale-105 transition-transform flex items-center space-x-2"
                    @click="openImagePreview(image, index)"
                  >
                    <FontAwesomeIcon :icon="faEye" />
                    <span>View Image</span>
                  </button>
                  <button
                    v-if="isEditMode"
                    class="bg-red-500 text-white px-4 py-2 rounded-xl font-semibold hover:scale-105 transition-transform flex items-center space-x-2"
                    @click="$emit('deleteImage', image.publitio_id)"
                  >
                    <FontAwesomeIcon :icon="faTrash" />
                    <span>Delete</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Image Info -->
            <div
              class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 lg:p-6 text-white"
            >
              <h3 class="font-bold text-lg mb-1">
                {{ image.caption || `Adventure #${index + 1}` }}
              </h3>
              <p class="text-white text-sm flex items-center space-x-1">
                <FontAwesomeIcon :icon="faMapMarkerAlt" class="text-xs" />
                <span>{{ image.location || 'Amazing journey captured' }}</span>
              </p>
            </div>
          </div>

          <!-- Upload Card (Visible in Edit Mode) -->
          <div
            v-if="isEditMode"
            class="group aspect-[4/3] border-3 border-dashed border-gray-300 rounded-2xl hover:border-primary transition-all duration-300 hover:bg-gray-50 flex flex-col items-center justify-center cursor-pointer"
            @click="$emit('openUpload')"
          >
            <div class="text-center p-6">
              <div
                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300"
              >
                <FontAwesomeIcon :icon="faUpload" class="text-primary text-2xl" />
              </div>
              <h3 class="font-bold text-primary text-lg mb-2">Add New Photo</h3>
              <p class="text-gray-600 text-sm">Click to upload your adventure photos</p>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!pageContent?.images?.length" class="text-center py-16 lg:py-20">
          <div
            class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6"
          >
            <FontAwesomeIcon :icon="faImages" class="text-primary text-6xl" />
          </div>
          <h3 class="text-2xl lg:text-3xl font-bold text-primary mb-4">No Images Yet</h3>
          <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            Start documenting your adventures to fill this gallery with amazing memories!
          </p>
          <button
            v-if="isEditMode"
            @click="$emit('openUpload')"
            class="bg-primary text-accent px-8 py-4 rounded-xl font-bold text-lg hover:scale-105 transition-all shadow-lg flex items-center space-x-3 mx-auto"
          >
            <FontAwesomeIcon :icon="faUpload" />
            <span>Upload Your First Photo</span>
          </button>
          <div v-else class="text-gray-500 text-sm">
            Check back later for amazing adventure photos!
          </div>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMoreImages && pageContent?.images?.length" class="text-center mt-12 lg:mt-16">
          <button
            @click="loadMore"
            class="border-2 border-primary text-primary px-8 py-4 rounded-xl font-bold text-lg hover:bg-primary hover:text-accent transition-all duration-300 flex items-center space-x-2 mx-auto"
          >
            <FontAwesomeIcon :icon="faPlus" />
            <span>Load More Adventures</span>
          </button>
        </div>

        <!-- All Images Loaded Message -->
        <div
          v-else-if="
            pageContent?.images?.length && displayedImages.length >= pageContent.images.length
          "
          class="text-center mt-8"
        >
          <p class="text-gray-500 dark:text-gray-400 text-sm">
            🎉 You've seen all {{ pageContent.images.length }} adventures!
          </p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 lg:py-20 bg-gradient-to-br from-gray-50 to-blue-50/30">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div
          class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6"
        >
          <FontAwesomeIcon :icon="faCamera" class="text-primary text-2xl" />
        </div>
        <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-6">
          Ready to Share Your Visual Story?
        </h2>
        <p class="text-lg lg:text-xl text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed">
          Upload your adventure photos and create beautiful visual narratives that inspire others.
        </p>
        <button
          v-if="isEditMode"
          @click="$emit('openUpload')"
          class="bg-primary text-accent px-8 py-4 rounded-xl font-bold text-lg hover:scale-105 transition-all shadow-xl flex items-center space-x-3 mx-auto"
        >
          <FontAwesomeIcon :icon="faUpload" />
          <span>Upload Your First Photo</span>
        </button>
      </div>
    </section>

    <!-- Image Preview Modal -->
    <div
      v-if="showImagePreview && selectedImage"
      class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4 animate-fade-in"
      @click="closeImagePreview"
    >
      <div class="relative max-w-7xl max-h-full w-full h-full flex items-center justify-center">
        <!-- Close Button -->
        <button
          class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors p-2"
          @click="closeImagePreview"
        >
          <FontAwesomeIcon :icon="faTimes" class="text-2xl" />
        </button>

        <!-- Navigation Buttons -->
        <button
          v-if="pageContent?.images?.length > 1"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors p-3 bg-black/50 rounded-full"
          @click.stop="prevImage"
        >
          <FontAwesomeIcon :icon="faArrowLeft" class="text-xl" />
        </button>

        <button
          v-if="pageContent?.images?.length > 1"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors p-3 bg-black/50 rounded-full"
          @click.stop="nextImage"
        >
          <FontAwesomeIcon :icon="faArrowRight" class="text-xl" />
        </button>

        <!-- Image Container -->
        <div class="relative max-w-full max-h-full" @click.stop>
          <img
            :src="selectedImage.url"
            :alt="selectedImage.caption || 'Adventure image'"
            class="max-w-full max-h-full object-contain rounded-lg"
          />

          <!-- Image Info Overlay -->
          <div
            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white"
          >
            <h3 class="text-xl font-bold mb-2">
              {{ selectedImage.caption || `Adventure #${currentImageIndex + 1}` }}
            </h3>
            <p class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faMapMarkerAlt" />
              <span>{{ selectedImage.location || 'Location not specified' }}</span>
            </p>
          </div>
        </div>

        <!-- Image Counter -->
        <div
          v-if="pageContent?.images?.length > 1"
          class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10 text-white bg-black/50 px-4 py-2 rounded-full text-sm"
        >
          {{ currentImageIndex + 1 }} / {{ pageContent.images.length }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .theme-page {
    color: var(--text-primary);
    background-color: var(--bg-primary);
  }

  .text-primary {
    color: var(--color-primary) !important;
  }
  .bg-primary {
    background-color: var(--color-primary) !important;
  }
  .border-primary {
    border-color: var(--color-primary) !important;
  }

  .text-secondary {
    color: var(--color-secondary) !important;
  }
  .bg-secondary {
    background-color: var(--color-secondary) !important;
  }
  .border-secondary {
    border-color: var(--color-secondary) !important;
  }

  .text-accent {
    color: var(--color-accent) !important;
  }
  .bg-accent {
    background-color: var(--color-accent) !important;
  }
  .border-accent {
    border-color: var(--color-accent) !important;
  }
  .animate-fade-in {
    animation: fadeIn 0.3s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }
</style>
