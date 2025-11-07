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
    faGlobeAmericas
} from '@fortawesome/free-solid-svg-icons';

defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean
});
</script>

<template>
    <div class="animate-fade-in-up">
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
            

                <!-- Image Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div 
                        v-for="(image, index) in pageContent?.images || []" 
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
                                    <button class="bg-accent text-primary px-4 py-2 rounded-xl font-semibold hover:scale-105 transition-transform flex items-center space-x-2">
                                        <FontAwesomeIcon :icon="faEye" />
                                        <span>View Story</span>
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
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 lg:p-6 text-accent">
                            <h3 class="font-bold text-lg mb-1">{{ image.caption || `Adventure #${index + 1}` }}</h3>
                            <p class="text-accent/80 text-sm flex items-center space-x-1">
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

                <!-- Load More -->
                <div v-if="pageContent?.images?.length" class="text-center mt-12 lg:mt-16">
                    <button class="border-2 border-primary text-primary px-8 py-4 rounded-xl font-bold text-lg hover:bg-primary hover:text-accent transition-all duration-300 flex items-center space-x-2 mx-auto">
                        <FontAwesomeIcon :icon="faPlus" />
                        <span>Load More Adventures</span>
                    </button>
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
    </div>
</template>