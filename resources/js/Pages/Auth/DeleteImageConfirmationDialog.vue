<template>
  <!-- Delete Confirmation Dialog -->
  <transition
    enter-active-class="transition-all duration-300 ease-out"
    leave-active-class="transition-all duration-200 ease-in"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-if="show"
      class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50 p-4"
    >
      <!-- Backdrop Click Handler -->
      <div class="absolute inset-0" @click="cancel"></div>

      <!-- Dialog Container -->
      <div
        class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 border border-gray-200"
      >
        <!-- Dialog Header -->
        <div class="p-8 text-center border-b border-gray-100 relative z-10">
          <!-- Icon Container -->
          <div class="relative inline-flex mb-6">
            <!-- Background Circle -->
            <div class="absolute inset-0 bg-gray-900 rounded-full opacity-5 animate-pulse"></div>
            <!-- Main Icon -->
            <div
              class="w-20 h-20 bg-gray-900 rounded-full flex items-center justify-center transform hover:scale-105 transition-transform duration-300 shadow-lg border border-gray-300"
            >
              <FontAwesomeIcon :icon="faTriangleExclamation" class="text-white text-2xl" />
            </div>
            <!-- Decorative Dots -->
            <div
              class="absolute -top-2 -right-2 w-4 h-4 bg-gray-600 rounded-full animate-bounce"
            ></div>
            <div
              class="absolute -bottom-2 -left-2 w-3 h-3 bg-gray-400 rounded-full animate-bounce delay-300"
            ></div>
          </div>

          <h3 class="text-3xl font-black text-gray-900 mb-3 tracking-tight">Confirm Deletion</h3>
          <p class="text-gray-600 text-base font-medium">
            This action is permanent and cannot be reversed
          </p>
        </div>

        <!-- Dialog Body -->
        <div class="p-8 text-center relative z-10">
          <!-- Image Preview -->
          <div class="flex justify-center mb-6">
            <div class="relative group">
              <div
                class="w-24 h-24 rounded-2xl border-4 border-gray-200 shadow-inner overflow-hidden bg-gray-50"
              >
                <img
                  :src="image?.url"
                  :alt="image?.caption"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
              </div>
              <!-- Overlay Icon -->
              <div
                class="absolute inset-0 bg-black/40 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
              >
                <FontAwesomeIcon :icon="faEye" class="text-white text-xl" />
              </div>
            </div>
          </div>

          <!-- Image Details -->
          <div class="space-y-3 mb-8">
            <div class="flex items-center justify-center space-x-2 text-gray-700">
              <FontAwesomeIcon :icon="faFileImage" class="text-gray-500" />
              <h4 class="text-xl font-bold truncate max-w-xs">
                {{ image?.caption || 'Untitled Image' }}
              </h4>
            </div>
            <div class="flex items-center justify-center space-x-2 text-gray-500">
              <FontAwesomeIcon :icon="faLocationDot" class="text-gray-400" />
              <p class="text-sm font-medium">
                {{ image?.location || 'No location specified' }}
              </p>
            </div>
          </div>

          <!-- Warning Section -->
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200">
            <div class="flex items-center justify-center space-x-3 mb-3">
              <FontAwesomeIcon :icon="faShieldHalved" class="text-gray-700 text-lg" />
              <span class="text-sm font-bold text-gray-800">PERMANENT ACTION</span>
            </div>
            <p class="text-sm text-gray-700 font-medium leading-relaxed">
              This image will be permanently deleted from your gallery. This operation cannot be
              undone or recovered.
            </p>
          </div>
        </div>

        <!-- Dialog Footer -->
        <div class="p-8 border-t border-gray-100 bg-gray-50/80 rounded-b-3xl relative z-10">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Cancel Button -->
            <button
              @click="cancel"
              :disabled="deleting"
              class="flex-1 px-8 py-4 border-2 border-gray-400 text-gray-700 rounded-2xl font-bold hover:bg-gray-100 hover:border-gray-600 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 shadow-sm group"
            >
              <div class="flex items-center justify-center space-x-3">
                <FontAwesomeIcon
                  :icon="faXmark"
                  class="text-gray-600 group-hover:scale-110 transition-transform duration-300"
                />
                <span class="text-base">Cancel</span>
              </div>
            </button>

            <!-- Delete Button -->
            <button
              @click="confirmDelete"
              :disabled="deleting"
              class="flex-1 px-8 py-4 bg-red-700 text-white rounded-2xl font-bold hover:bg-red-800 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 shadow-lg hover:shadow-xl relative overflow-hidden group"
            >
              <!-- Button Content -->
              <div class="bg-transparent relative flex items-center justify-center space-x-3">
                <FontAwesomeIcon v-if="deleting" :icon="faSpinner" class="animate-spin text-lg" />
                <FontAwesomeIcon
                  v-else
                  :icon="faTrashCan"
                  class="group-hover:scale-110 transition-transform duration-300"
                />
                <span class="text-base font-semibold">
                  {{ deleting ? 'Deleting...' : 'Delete ' }}
                </span>
              </div>
            </button>
          </div>
        </div>

        <!-- Corner Accents -->
        <div class="absolute top-4 left-4 w-3 h-3 bg-gray-300 rounded-full opacity-50"></div>
        <div class="absolute top-4 right-4 w-3 h-3 bg-gray-300 rounded-full opacity-50"></div>
        <div class="absolute bottom-4 left-4 w-3 h-3 bg-gray-300 rounded-full opacity-50"></div>
        <div class="absolute bottom-4 right-4 w-3 h-3 bg-gray-300 rounded-full opacity-50"></div>
      </div>
    </div>
  </transition>
</template>

<script setup>
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faTriangleExclamation,
    faTrashCan,
    faSpinner,
    faXmark,
    faEye,
    faFileImage,
    faLocationDot,
    faShieldHalved,
  } from '@fortawesome/free-solid-svg-icons';

  // Props
  const props = defineProps({
    show: Boolean,
    image: Object,
    deleting: Boolean,
  });

  // Emits
  const emit = defineEmits(['confirm', 'cancel']);

  // Methods
  const confirmDelete = () => {
    emit('confirm');
  };

  const cancel = () => {
    emit('cancel');
  };
</script>

<style scoped>
  /* Enhanced backdrop blur */
  @supports (backdrop-filter: blur(12px)) {
    .backdrop-blur-md {
      backdrop-filter: blur(12px);
    }
  }

  /* Smooth animations */
  button {
    transform: translateZ(0);
    backface-visibility: hidden;
    perspective: 1000px;
  }

  /* Custom bounce animation */
  @keyframes gentle-bounce {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-3px);
    }
  }

  .animate-bounce {
    animation: gentle-bounce 2s ease-in-out infinite;
  }

  /* Hover lift effect */
  .hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
  }

  /* Focus states for accessibility */
  button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
  }

  /* Smooth image scaling */
  img {
    transform: translateZ(0);
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  }
</style>
