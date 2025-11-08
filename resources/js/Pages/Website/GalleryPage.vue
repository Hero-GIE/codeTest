<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faCamera,
    faMountain,
    faUmbrellaBeach,
    faCity,
    faStar,
    faPlus,
    faUpload,
    faEye,
    faImages,
    faMapMarkerAlt,
    faGlobeAmericas,
    faTimes,
    faArrowLeft,
    faArrowRight,
    faTrash
} from '@fortawesome/free-solid-svg-icons';
import { ref, onMounted, onUnmounted, computed } from 'vue';

// Define props and make them reactive
const props = defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean
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
    
    switch(event.key) {
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
</script>

<template>
    <div class="animate-fade-in-up theme-page">
        <!-- Gallery Hero -->
        <section class="bg-gradient-to-br from-primary to-secondary text-accent py-20 lg:py-28 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <!-- Background image -->
                <div class="absolute inset-0 bg-cover bg-center" 
                     style="background-image: url('https://plus.unsplash.com/premium_photo-1709371824843-2b72258fbd71?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1267');">
                </div>

                <!-- Black overlay -->
                <div class="absolute inset-0 bg-black opacity-50"></div>
            </div>

            <!-- Foreground content -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Badge -->
                <div class="mb-8 inline-block animate-fade-in-down">
                    <span class="bg-white/10 text-white px-6 py-3 rounded-full text-sm font-semibold backdrop-blur-md border border-white/30 inline-flex items-center space-x-2">
                        <FontAwesomeIcon :icon="faStar" class="text-yellow-300" />
                        <span>Welcome to Your Adventure Log</span>
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl text-white lg:text-6xl font-bold mb-6">
                    {{ pageContent?.title || 'Adventure Gallery' }}
                </h1>
                <p class="text-lg md:text-xl  text-white opacity-90 max-w-2xl mx-auto leading-relaxed">
                    Visual stories from incredible journeys around the world
                </p>
            </div>
        </section>

        <!-- Gallery Grid -->
        <section class="py-16 lg:py-20 bg-accent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Image Counter -->
                <div class="text-center mb-8">
                    <p class="text-gray-600 dark:text-gray-400">
                        Showing {{ displayedImages.length }} of {{ pageContent?.images?.length || 0 }} adventures
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
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                <div class="transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 flex space-x-3">
                                    <button 
                                        class="bg-accent text-white px-4 py-2 rounded-xl font-semibold hover:scale-105 transition-transform flex items-center space-x-2"
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
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 lg:p-6 text-white">
                            <h3 class="font-bold text-lg mb-1">{{ image.caption || `Adventure #${index + 1}` }}</h3>
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
                            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <FontAwesomeIcon :icon="faUpload" class="text-primary text-2xl" />
                            </div>
                            <h3 class="font-bold text-primary text-lg mb-2">Add New Photo</h3>
                            <p class="text-gray-600 text-sm">Click to upload your adventure photos</p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!pageContent?.images?.length" class="text-center py-16 lg:py-20">
                    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
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
                <div v-else-if="pageContent?.images?.length && displayedImages.length >= pageContent.images.length" 
                     class="text-center mt-8">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        🎉 You've seen all {{ pageContent.images.length }} adventures!
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-20 bg-gradient-to-br from-gray-50 to-blue-50/30">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <FontAwesomeIcon :icon="faCamera" class="text-primary text-2xl" />
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-6">Ready to Share Your Visual Story?</h2>
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
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
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

.text-primary { color: var(--color-primary) !important; }
.bg-primary { background-color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }

.text-secondary { color: var(--color-secondary) !important; }
.bg-secondary { background-color: var(--color-secondary) !important; }
.border-secondary { border-color: var(--color-secondary) !important; }

.text-accent { color: var(--color-accent) !important; }
.bg-accent { background-color: var(--color-accent) !important; }
.border-accent { border-color: var(--color-accent) !important; }
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