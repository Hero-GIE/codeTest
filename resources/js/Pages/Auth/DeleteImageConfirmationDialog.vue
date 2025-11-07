<template>
    <!-- Delete Confirmation Dialog -->
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 scale-100">
            <!-- Dialog Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <FontAwesomeIcon :icon="faTrash" class="text-red-600 text-lg" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Delete Image</h3>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <!-- Dialog Body -->
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <img 
                        :src="image?.url" 
                        :alt="image?.caption" 
                        class="w-16 h-16 object-cover rounded-lg border border-gray-200"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ image?.caption || 'Untitled Image' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ image?.location || 'No location' }}
                        </p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600">
                    Are you sure you want to delete this image? This will permanently remove it from your gallery.
                </p>
            </div>

            <!-- Dialog Footer -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <div class="flex space-x-3">
                    <button
                        @click="cancel"
                        :disabled="deleting"
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="deleting"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                    >
                        <FontAwesomeIcon 
                            v-if="deleting" 
                            :icon="faSpinner" 
                            class="animate-spin" 
                        />
                        <span>{{ deleting ? 'Deleting...' : 'Delete Image' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTrash, faSpinner } from '@fortawesome/free-solid-svg-icons';

// Props
const props = defineProps({
    show: Boolean,
    image: Object,
    deleting: Boolean
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
/* Smooth backdrop blur for modern browsers */
@supports (backdrop-filter: blur(10px)) {
    .bg-black.bg-opacity-50 {
        backdrop-filter: blur(10px);
    }
}
</style>