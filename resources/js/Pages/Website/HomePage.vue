<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
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
    faPlus
} from '@fortawesome/free-solid-svg-icons'
import { ref, computed } from 'vue'

// Define props first
const props = defineProps({
    pageContent: {
        type: Object,
        default: () => ({})
    },
    user: {
        type: Object,
        default: null
    },
    isEditMode: {
        type: Boolean,
        default: false
    }
})

// Default content in case pageContent is not provided
const defaultPageContent = {
    title: 'Adventure Log',
    subtitle: 'Document and share your amazing journeys with the world',
    sections: {
        hero: {
            title: 'About Our',
            subtitle: 'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.'
        },
        features: {
            title: 'Amazing Features',
            items: [
                {
                    title: 'Interactive Maps',
                    description: 'Track and visualize your adventures with beautiful interactive maps that showcase your journey routes and key locations.'
                },
                {
                    title: 'Photo Gallery',
                    description: 'Create stunning visual stories with our easy-to-use photo gallery that highlights your best moments and memories.'
                },
                {
                    title: 'Community Stories',
                    description: 'Connect with fellow adventurers, share experiences, and get inspired by stories from around the globe.'
                }
            ]
        },
        recent: {
            title: 'Recent Adventures',
            posts: []
        },
        mission: {
            title: 'Our Mission',
            content: 'We believe every adventure has a story worth telling. Our mission is to provide the tools and platform for adventurers to document, share, and inspire others with their journeys.',
            stats: [
                { number: '0+', label: 'Adventures Logged' },
                { number: '0+', label: 'Countries Covered' },
                { number: '0+', label: 'Photos Shared' },
                { number: '1', label: 'Happy Explorer' }
            ]
        }
    }
}

// Deep merge function to properly handle nested objects
function deepMerge(target, source) {
    const result = { ...target }
    
    for (const key in source) {
        if (source[key] instanceof Object && key in target && target[key] instanceof Object) {
            result[key] = deepMerge(target[key], source[key])
        } else {
            result[key] = source[key]
        }
    }
    
    return result
}

// Merge provided pageContent with defaults using deep merge
const pageData = deepMerge(defaultPageContent, props.pageContent || {})

// Computed property to check if user has any adventures
const hasAdventures = computed(() => {
    return pageData.sections.recent.posts && pageData.sections.recent.posts.length > 0
})

// Method to create new adventure
const createNewAdventure = () => {
    // Redirect to adventure creation page or open modal
    window.location.href = '/dashboard/adventures/create'
}

// Method to edit adventure
const editAdventure = (adventureId) => {
    if (props.isEditMode) {
        window.location.href = `/dashboard/adventures/${adventureId}/edit`
    }
}
</script>

<template>
    <div class="overflow-hidden">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-secondary via-primary to-secondary text-white py-16 md:py-24 overflow-hidden">
            <!-- Background Image with Dim Overlay -->
            <div class="absolute inset-0">
                <img 
                    src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80" 
                    alt="Mountain landscape" 
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-br from-primary/80 via-secondary/70 to-primary/80"></div>
                <div class="absolute inset-0 bg-black/60"></div>
            </div>

            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-40 right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
                <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-white/5 rounded-full blur-3xl animate-pulse delay-2000"></div>
            </div>

            <!-- Floating Icons -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/4 left-10 text-white/20 text-4xl animate-float">
                    <FontAwesomeIcon :icon="faCompass" />
                </div>
                <div class="absolute top-1/3 right-16 text-white/20 text-5xl animate-float-delayed">
                    <FontAwesomeIcon :icon="faMountain" />
                </div>
                <div class="absolute bottom-1/4 left-1/4 text-white/20 text-3xl animate-float-slow">
                    <FontAwesomeIcon :icon="faUsers" />
                </div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Badge -->
                <div class="mb-8 inline-block animate-fade-in-down">
                    <span class="bg-white/10 text-white px-6 py-3 rounded-full text-sm font-semibold backdrop-blur-md border border-white/30 inline-flex items-center space-x-2">
                        <FontAwesomeIcon :icon="faStar" class="text-yellow-300" />
                        <span>Welcome to Your Adventure Log</span>
                    </span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-4 leading-tight animate-fade-in-up">
                    <span class="block mb-2">
                        {{ pageData.sections.hero.title }}
                    </span>
                  
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-xl opacity-95 max-w-4xl mx-auto leading-relaxed font-light animate-fade-in-up delay-200">
                    {{ pageData.sections.hero.subtitle }}
                </p>

              

                <!-- Stats Section -->
                <div class="mt-16 grid grid-cols-3 gap-8 max-w-3xl mx-auto animate-fade-in-up delay-600">
                    <div class="text-center">
                        <div class="text-4xl font-black text-white mb-2">{{ pageData.sections.mission.stats[0].number }}</div>
                        <div class="text-white/80 text-sm">Adventures Logged</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-white mb-2">{{ pageData.sections.mission.stats[1].number }}</div>
                        <div class="text-white/80 text-sm">Countries Visited</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-white mb-2">{{ pageData.sections.mission.stats[3].number }}</div>
                        <div class="text-white/80 text-sm">Happy Explorer</div>
                    </div>
                </div>
            </div>
            
        </section>

        <!-- Mission Section -->
        <section class="py-20 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-[0.02]">
                <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80');"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="animate-fade-in-up">
                        <div class="inline-block mb-6">
                            <span class="bg-primary/10 text-primary px-5 py-2 rounded-full text-sm font-bold flex items-center space-x-2">
                                <FontAwesomeIcon :icon="faGlobe" />
                                <span>MY MISSION</span>
                            </span>
                        </div>
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-primary mb-6 leading-tight">
                            Why I Adventure
                        </h2>
                        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                            {{ pageData.sections.mission.content }}
                        </p>
                        
                        <!-- Mission Points -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 rounded-xl bg-white shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center flex-shrink-0">
                                    <FontAwesomeIcon :icon="faRoute" class="text-white text-lg" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Track Every Journey</h4>
                                    <p class="text-gray-600 text-sm">From city explorations to mountain treks</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl bg-white shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center flex-shrink-0">
                                    <FontAwesomeIcon :icon="faBinoculars" class="text-white text-lg" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Share My Perspective</h4>
                                    <p class="text-gray-600 text-sm">Inspire others with my unique experiences</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-6 animate-fade-in-up delay-200">
                        <div 
                            v-for="(stat, index) in pageData.sections.mission.stats" 
                            :key="index"
                            class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100"
                        >
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-black text-primary mb-2 group-hover:text-secondary transition-colors">
                                    {{ stat.number }}
                                </div>
                                <div class="text-gray-600 font-medium">
                                    {{ stat.label }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid with Modern Cards -->
        <section class="py-20 bg-gradient-to-b from-accent to-gray-50 relative">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary via-secondary to-primary"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-6">
                        <span class="bg-primary/10 text-primary px-5 py-2 rounded-full text-sm font-bold">
                            ✨ FEATURES
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-primary mb-8 leading-tight">
                        {{ pageData.sections.features.title }}
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Everything you need to document and share your amazing adventures with the world
                    </p>
                </div>
                
                <!-- Feature Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div 
                        v-for="(feature, index) in pageData.sections.features.items" 
                        :key="index"
                        class="group relative bg-white p-8 rounded-3xl border-2 border-gray-100 hover:border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
                    >
                        <!-- Icon with Gradient Background -->
                        <div class="mb-6 inline-flex">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary rounded-2xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-primary to-secondary text-accent p-5 rounded-2xl text-3xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                    <FontAwesomeIcon 
                                        :icon="index === 0 ? faMapMarkedAlt : index === 1 ? faCamera : faUsers" 
                                    />
                                </div>
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-primary mb-4 group-hover:text-secondary transition-colors">
                            {{ feature.title }}
                        </h3>
                        <p class="text-gray-600 text-lg leading-relaxed mb-6">
                            {{ feature.description }}
                        </p>

                        <!-- Learn More Link -->
                        <div class="flex items-center text-primary font-semibold group-hover:text-secondary transition-colors">
                            <span class="mr-2">Learn more</span>
                            <FontAwesomeIcon :icon="faArrowRight" class="transform group-hover:translate-x-2 transition-transform" />
                        </div>

                        <!-- Decorative Corner -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-3xl rounded-tr-3xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Adventures with Enhanced Cards -->
        <section class="py-20 bg-gradient-to-br from-gray-50 via-accent to-gray-50 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div
                    class="absolute inset-0"
                    style="
                        background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0);
                        background-size: 40px 40px;
                    "
                ></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-6">
                        <span class="bg-secondary/10 text-secondary px-5 py-2 rounded-full text-sm font-bold flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faChartLine" />
                            <span>MY RECENT ADVENTURES</span>
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-primary mb-8 leading-tight">
                        {{ pageData.sections.recent.title }}
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        My latest journeys and experiences from around the world
                    </p>
                </div>

                <!-- Adventure Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
                    <div 
                        v-for="(post, index) in pageData.sections.recent.posts" 
                        :key="post.id || index"
                        class="group bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-2 cursor-pointer"
                        @click="editAdventure(post.id)"
                    >
                        <!-- Image with Overlay -->
                        <div class="relative h-64 bg-gradient-to-br from-primary via-secondary to-primary overflow-hidden">
                            <img 
                                :src="post.image" 
                                :alt="post.title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                            
                            <!-- Floating Icon -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-6xl text-accent/80 transform group-hover:scale-110 transition-transform">
                                    <FontAwesomeIcon :icon="faMountainSun" />
                                </div>
                            </div>

                            <!-- Date Badge -->
                            <div class="absolute top-6 right-6 bg-accent/95 backdrop-blur-sm text-primary px-4 py-2 rounded-xl text-sm font-bold shadow-lg flex items-center space-x-2">
                                <span>📅</span>
                                <span>{{ post.date }}</span>
                            </div>

                            <!-- Like Badge -->
                            <div class="absolute bottom-6 left-6 bg-accent/95 backdrop-blur-sm text-primary px-4 py-2 rounded-xl text-sm font-bold shadow-lg flex items-center space-x-2">
                                <FontAwesomeIcon :icon="faHeart" class="text-red-500" />
                                <span>{{ 100 + index * 50 }}</span>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-primary mb-4 group-hover:text-secondary transition-colors leading-tight">
                                {{ post.title }}
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ post.excerpt }}
                            </p>
                            
                            <!-- Read More Button -->
                            <button class="group/btn inline-flex items-center space-x-3 text-primary font-bold hover:text-secondary transition-colors">
                                <span>Read Full Story</span>
                                <FontAwesomeIcon :icon="faArrowRight" class="transform group-hover/btn:translate-x-2 transition-transform" />
                            </button>
                        </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div 
                        v-if="!hasAdventures"
                        class="col-span-2 text-center py-16"
                    >
                        <div class="text-8xl mb-6 text-gray-300">
                            <FontAwesomeIcon :icon="faCompass" />
                        </div>
                        <h3 class="text-3xl font-bold text-gray-500 mb-4">No Adventures Yet</h3>
                        <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">
                            Start documenting your journeys to see them here! Share your stories, photos, and experiences with the world.
                        </p>
                        <button 
                            @click="createNewAdventure"
                            class="bg-primary text-black px-8 py-4 rounded-2xl font-bold text-lg hover:bg-secondary transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center space-x-3"
                        >
                            <FontAwesomeIcon :icon="faPlus" />
                            <span>Create Your First Adventure</span>
                        </button>
                    </div>
                </div>

                <!-- CTA Section with Gradient Card -->
                <div class="relative" v-if="hasAdventures">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-primary rounded-3xl blur-2xl opacity-20"></div>
                    <div class="relative bg-gradient-to-br from-primary via-secondary to-primary text-accent rounded-3xl p-12 md:p-16 max-w-5xl mx-auto overflow-hidden">
                        <!-- Decorative Circles -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-accent/10 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-accent/10 rounded-full -ml-24 -mb-24"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="inline-block mb-6">
                                <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-5xl" />
                            </div>
                            <h3 class="text-3xl md:text-4xl font-black mb-6 leading-tight">
                                Ready to Share Your Next Story?
                            </h3>
                            <p class="text-lg md:text-xl opacity-95 mb-10 max-w-2xl mx-auto leading-relaxed">
                                Keep building your adventure log and inspire others with your journeys
                            </p>
                            <button 
                                @click="createNewAdventure"
                                class="group bg-accent text-primary px-12 py-5 rounded-2xl font-black text-lg hover:scale-105 transition-all duration-300 shadow-2xl inline-flex items-center space-x-4"
                            >
                                <FontAwesomeIcon :icon="faPlus" class="text-xl group-hover:translate-x-1 transition-transform" />
                                <span>Add New Adventure</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) rotate(5deg);
    }
}

@keyframes float-slow {
    0%, 100% {
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

/* Shadow Variations */
.shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>