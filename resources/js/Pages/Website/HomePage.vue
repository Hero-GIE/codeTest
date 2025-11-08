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
    faPlus,
    faEdit,
    faSave,
    faTimes
} from '@fortawesome/free-solid-svg-icons'
import { ref, computed, watch, onMounted } from 'vue'

// ✅ FIXED: Define props FIRST and store in a variable
const props = defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean,
    publishedPages: Array,
    websiteSettings: Object
});

// ✅ FIXED: Now you can use props.websiteSettings
const themeColors = computed(() => {
    return props.websiteSettings?.customColors || {
        primary: '#000000',
        secondary: '#8B4513', 
        accent: '#FFFFFF'
    };
});

// Helper function to get CSS variable value
const getCssVariable = (variableName) => {
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
};

// Emit events for saving changes
const emit = defineEmits(['update:pageContent', 'save'])

// Local editable content
const editableContent = ref({})
const editingSection = ref(null)
const editingField = ref(null)

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

// Initialize editable content
function initializeEditableContent() {
    editableContent.value = deepMerge(defaultPageContent, props.pageContent || {})
}

// Watch for changes in pageContent prop
watch(() => props.pageContent, () => {
    initializeEditableContent()
}, { immediate: true })

// Computed property to check if user has any adventures
const hasAdventures = computed(() => {
    return editableContent.value.sections?.recent?.posts && editableContent.value.sections.recent.posts.length > 0
})

// Editing methods
const startEditing = (section, field = null, index = null) => {
    if (!props.isEditMode) return
    
    if (index !== null) {
        editingSection.value = `${section}[${index}]`
    } else {
        editingSection.value = section
    }
    editingField.value = field
}

const stopEditing = () => {
    editingSection.value = null
    editingField.value = null
    // Emit changes when editing stops
    emit('update:pageContent', editableContent.value)
}

const saveChanges = () => {
    emit('save', editableContent.value)
    stopEditing()
}

const addFeatureItem = () => {
    if (!editableContent.value.sections.features.items) {
        editableContent.value.sections.features.items = []
    }
    editableContent.value.sections.features.items.push({
        title: 'New Feature',
        description: 'Describe this amazing feature...'
    })
}

const removeFeatureItem = (index) => {
    if (editableContent.value.sections.features.items) {
        editableContent.value.sections.features.items.splice(index, 1)
    }
}

const addMissionStat = () => {
    if (!editableContent.value.sections.mission.stats) {
        editableContent.value.sections.mission.stats = []
    }
    editableContent.value.sections.mission.stats.push({
        number: '0+',
        label: 'New Stat'
    })
}

const removeMissionStat = (index) => {
    if (editableContent.value.sections.mission.stats) {
        editableContent.value.sections.mission.stats.splice(index, 1)
    }
}

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

// Helper to check if currently editing
const isEditing = (section, field = null, index = null) => {
    if (index !== null) {
        return editingSection.value === `${section}[${index}]` && editingField.value === field
    }
    return editingSection.value === section && editingField.value === field
}

// ✅ ADD: Apply theme colors when component mounts
onMounted(() => {
    // Debug: Check if theme colors are being applied
    console.log('HomePage theme colors:', themeColors.value);
});
</script>

<template>
    <div class="overflow-hidden relative theme-page" :class="{ 'editing-mode': isEditMode }">
        <!-- Edit Mode Indicator -->
        <div v-if="isEditMode" class="fixed top-4 right-4 z-50 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
            <FontAwesomeIcon :icon="faEdit" />
            <span class="font-bold">Edit Mode</span>
        </div>

        <!-- Hero Section - UPDATED to use theme utility classes -->
        <section class="relative theme-gradient-primary text-white py-16 md:py-24 overflow-hidden">
            <!-- Background Image with Dim Overlay -->
            <div class="absolute inset-0">
                <img 
                    src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80" 
                    alt="Mountain landscape" 
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 theme-gradient-primary opacity-80"></div>
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
                <div class="mb-8 inline-block animate-fade-in-down relative" :class="{ 'editable-section': isEditMode }">
                    <span 
                        class="bg-white/10 text-white px-6 py-3 rounded-full text-sm font-semibold backdrop-blur-md border border-white/30 inline-flex items-center space-x-2"
                        @click="isEditMode && startEditing('badge')"
                    >
                        <FontAwesomeIcon :icon="faStar" class="text-yellow-300" />
                        <span>Welcome to Your Adventure Log</span>
                    </span>
                    <div v-if="isEditMode" class="edit-controls absolute -top-2 -right-2 flex space-x-1">
                        <button class="bg-blue-500 text-white p-1 rounded-full text-xs">
                            <FontAwesomeIcon :icon="faEdit" />
                        </button>
                    </div>
                </div>

                <!-- Main Heading -->
                <h1 
                    class="text-4xl md:text-5xl lg:text-6xl font-black mb-4 leading-tight animate-fade-in-up relative"
                    :class="{ 'editable-section': isEditMode }"
                    @click="isEditMode && startEditing('hero', 'title')"
                >
                    <span class="block mb-2">
                        <span v-if="!isEditing('hero', 'title')">{{ editableContent.sections?.hero?.title }}</span>
                        <input
                            v-else
                            v-model="editableContent.sections.hero.title"
                            @blur="stopEditing"
                            @keyup.enter="stopEditing"
                            class="bg-transparent border-b-2 border-white outline-none text-center w-full"
                            autofocus
                        />
                    </span>
                </h1>

                <!-- Subtitle -->
                <p 
                    class="text-xl md:text-xl opacity-95 max-w-4xl mx-auto leading-relaxed font-light animate-fade-in-up delay-200 relative"
                    :class="{ 'editable-section': isEditMode }"
                    @click="isEditMode && startEditing('hero', 'subtitle')"
                >
                    <span v-if="!isEditing('hero', 'subtitle')">{{ editableContent.sections?.hero?.subtitle }}</span>
                    <textarea
                        v-else
                        v-model="editableContent.sections.hero.subtitle"
                        @blur="stopEditing"
                        @keyup.enter="stopEditing"
                        class="bg-transparent border-b-2 border-white outline-none text-center w-full resize-none"
                        rows="3"
                        autofocus
                    />
                </p>

                <!-- Stats Section -->
                <div class="mt-16 grid grid-cols-3 gap-8 max-w-3xl mx-auto animate-fade-in-up delay-600">
                    <div 
                        v-for="(stat, index) in editableContent.sections?.mission?.stats" 
                        :key="index"
                        class="text-center relative"
                        :class="{ 'editable-section': isEditMode }"
                    >
                        <div 
                            class="text-4xl font-black text-white mb-2"
                            @click="isEditMode && startEditing('missionStats', 'number', index)"
                        >
                            <span v-if="!isEditing('missionStats', 'number', index)">{{ stat.number }}</span>
                            <input
                                v-else
                                v-model="stat.number"
                                @blur="stopEditing"
                                @keyup.enter="stopEditing"
                                class="bg-transparent border-b-2 border-white outline-none text-center w-20"
                                autofocus
                            />
                        </div>
                        <div 
                            class="text-white/80 text-sm"
                            @click="isEditMode && startEditing('missionStats', 'label', index)"
                        >
                            <span v-if="!isEditing('missionStats', 'label', index)">{{ stat.label }}</span>
                            <input
                                v-else
                                v-model="stat.label"
                                @blur="stopEditing"
                                @keyup.enter="stopEditing"
                                class="bg-transparent border-b-2 border-white outline-none text-center w-full"
                                autofocus
                            />
                        </div>
                        <div v-if="isEditMode" class="edit-controls absolute -top-2 -right-2 flex space-x-1">
                            <button 
                                @click="removeMissionStat(index)"
                                class="bg-red-500 text-white p-1 rounded-full text-xs"
                            >
                                <FontAwesomeIcon :icon="faTimes" />
                            </button>
                        </div>
                    </div>
                    <div v-if="isEditMode" class="col-span-3">
                        <button 
                            @click="addMissionStat"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center space-x-2 mx-auto"
                        >
                            <FontAwesomeIcon :icon="faPlus" />
                            <span>Add Stat</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission Section - UPDATED to use theme classes -->
        <section class="py-20 theme-bg-accent relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-[0.02]">
                <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80');"></div>
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
                                <span>MY MISSION</span>
                            </span>
                        </div>
                        
                        <h2 
                            class="text-4xl md:text-5xl lg:text-6xl font-black theme-text-primary mb-6 leading-tight relative"
                            :class="{ 'editable-section': isEditMode }"
                            @click="isEditMode && startEditing('mission', 'title')"
                        >
                            <span v-if="!isEditing('mission', 'title')">Why I Adventure</span>
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
                            <span v-if="!isEditing('mission', 'content')">{{ editableContent.sections?.mission?.content }}</span>
                            <textarea
                                v-else
                                v-model="editableContent.sections.mission.content"
                                @blur="stopEditing"
                                @keyup.enter="stopEditing"
                                class="bg-transparent border-b-2 theme-border-secondary outline-none w-full resize-none"
                                rows="4"
                                autofocus
                            />
                        </p>
                        
                        <!-- Mission Points -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 rounded-xl theme-bg-accent shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-12 h-12 theme-bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                                    <FontAwesomeIcon :icon="faRoute" class="theme-text-accent text-lg" />
                                </div>
                                <div>
                                    <h4 class="font-bold theme-text-primary">Track Every Journey</h4>
                                    <p class="theme-text-secondary text-sm">From city explorations to mountain treks</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl theme-bg-accent shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-12 h-12 theme-bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                                    <FontAwesomeIcon :icon="faBinoculars" class="theme-text-accent text-lg" />
                                </div>
                                <div>
                                    <h4 class="font-bold theme-text-primary">Share My Perspective</h4>
                                    <p class="theme-text-secondary text-sm">Inspire others with my unique experiences</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-6 animate-fade-in-up delay-200">
                        <div 
                            v-for="(stat, index) in editableContent.sections?.mission?.stats" 
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

        <!-- Features Grid with Modern Cards - UPDATED to use theme classes -->
        <section class="py-20 theme-bg-primary relative">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 theme-gradient-primary"></div>
            
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
                        <span v-if="!isEditing('features', 'title')">{{ editableContent.sections?.features?.title }}</span>
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
                        v-for="(feature, index) in editableContent.sections?.features?.items" 
                        :key="index"
                        class="group relative theme-bg-accent p-8 rounded-3xl border-2 theme-border-secondary hover:theme-border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
                        :class="{ 'editable-section': isEditMode }"
                    >
                        <!-- Icon with Gradient Background -->
                        <div class="mb-6 inline-flex">
                            <div class="relative">
                                <div class="absolute inset-0 theme-gradient-primary rounded-2xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative theme-gradient-primary theme-text-accent p-5 rounded-2xl text-3xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                    <FontAwesomeIcon 
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
                            <span v-if="!isEditing('featureItem', 'description', index)">{{ feature.description }}</span>
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
                        <div class="flex items-center theme-text-primary font-semibold group-hover:theme-text-secondary transition-colors">
                            <span class="mr-2">Learn more</span>
                            <FontAwesomeIcon :icon="faArrowRight" class="transform group-hover:translate-x-2 transition-transform" />
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
                        <div class="absolute top-0 right-0 w-20 h-20 theme-bg-primary/5 rounded-bl-3xl rounded-tr-3xl"></div>
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

        <!-- Recent Adventures Section - UPDATED to use theme classes -->
        <section class="py-12 sm:py-16 lg:py-20 theme-bg-primary relative overflow-hidden">
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
                <div class="text-center mb-12 sm:mb-16">
                    <div class="inline-block mb-4 sm:mb-6 relative" :class="{ 'editable-section': isEditMode }">
                        <span 
                            class="theme-bg-secondary/10 theme-text-secondary px-4 sm:px-5 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold flex items-center justify-center space-x-1 sm:space-x-2"
                            @click="isEditMode && startEditing('recentBadge')"
                        >
                            <FontAwesomeIcon :icon="faChartLine" class="text-xs sm:text-sm" />
                            <span>MY RECENT ADVENTURES</span>
                        </span>
                    </div>
                    
                    <h2 
                        class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-black theme-text-primary mb-4 sm:mb-6 lg:mb-8 leading-tight relative"
                        :class="{ 'editable-section': isEditMode }"
                        @click="isEditMode && startEditing('recent', 'title')"
                    >
                        <span v-if="!isEditing('recent', 'title')">{{ editableContent.sections?.recent?.title }}</span>
                        <input
                            v-else
                            v-model="editableContent.sections.recent.title"
                            @blur="stopEditing"
                            @keyup.enter="stopEditing"
                            class="bg-transparent border-b-2 theme-border-primary outline-none text-center w-full"
                            autofocus
                        />
                    </h2>
                    
                    <p class="text-base sm:text-lg lg:text-xl theme-text-secondary max-w-3xl mx-auto leading-relaxed px-2 sm:px-0">
                        My latest journeys and experiences from around the world
                    </p>
                </div>

                <!-- Adventure Cards Grid - Show either cards OR empty state -->
                <div class="mb-12 sm:mb-16">
                    <!-- Show Adventure Cards when there ARE adventures -->
                    <div v-if="hasAdventures" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                        <div 
                            v-for="(post, index) in editableContent.sections?.recent?.posts" 
                            :key="post.id || index"
                            class="group theme-bg-accent rounded-2xl sm:rounded-3xl shadow-lg sm:shadow-xl hover:shadow-2xl sm:hover:shadow-3xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2 cursor-pointer"
                            @click="editAdventure(post.id)"
                        >
                            <!-- Image with Overlay -->
                            <div class="relative h-48 sm:h-56 lg:h-64 theme-gradient-primary overflow-hidden">
                                <img 
                                    :src="post.image" 
                                    :alt="post.title"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                                
                                <!-- Floating Icon -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-4xl sm:text-5xl lg:text-6xl text-white transform group-hover:scale-105 sm:group-hover:scale-110 transition-transform">
                                        <FontAwesomeIcon :icon="faMountainSun" />
                                    </div>
                                </div>

                                <!-- Date Badge -->
                                <div class="absolute top-3 sm:top-4 lg:top-6 right-3 sm:right-4 lg:right-6 theme-bg-accent backdrop-blur-sm theme-text-primary px-2 sm:px-3 lg:px-4 py-1 sm:py-1.5 lg:py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold shadow-lg flex items-center space-x-1 sm:space-x-2">
                                    <span class="text-sm">📅</span>
                                    <span class="text-xs sm:text-sm">{{ post.date }}</span>
                                </div>

                                <!-- Like Badge -->
                                <div class="absolute bottom-3 sm:bottom-4 lg:bottom-6 left-3 sm:left-4 lg:left-6 theme-bg-accent/95 backdrop-blur-sm theme-text-primary px-2 sm:px-3 lg:px-4 py-1 sm:py-1.5 lg:py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold shadow-lg flex items-center space-x-1 sm:space-x-2">
                                    <FontAwesomeIcon :icon="faHeart" class="text-red-500 text-xs sm:text-sm" />
                                    <span class="text-xs sm:text-sm">{{ 100 + index * 50 }}</span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-4 sm:p-6 lg:p-8">
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold theme-text-primary mb-2 sm:mb-3 lg:mb-4 group-hover:theme-text-secondary transition-colors leading-tight line-clamp-2">
                                    {{ post.title }}
                                </h3>
                                <p class="theme-text-secondary mb-3 sm:mb-4 lg:mb-6 leading-relaxed text-sm sm:text-base line-clamp-3">
                                    {{ post.excerpt }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Show Empty State when there are NO adventures -->
                    <div 
                        v-else
                        class="text-center py-8 sm:py-12 lg:py-16 px-4"
                    >
                        <div class="text-6xl sm:text-7xl lg:text-8xl mb-4 sm:mb-6 text-gray-300">
                            <FontAwesomeIcon :icon="faCompass" />
                        </div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold theme-text-secondary mb-3 sm:mb-4">No Adventures Yet</h3>
                        <p class="text-base sm:text-lg lg:text-xl theme-text-secondary mb-6 sm:mb-8 max-w-2xl mx-auto leading-relaxed">
                            Start documenting your journeys to see them here! Share your stories, photos, and experiences with the world.
                        </p>
                        <button 
                            @click="createNewAdventure"
                            class="theme-bg-primary theme-text-accent px-6 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg hover:theme-bg-secondary transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto justify-center"
                        >
                            <FontAwesomeIcon :icon="faPlus" class="theme-text-accent text-sm sm:text-base" />
                            <span>Create Your First Adventure</span>
                        </button>
                    </div>
                </div>

                <!-- CTA Section (shown when there ARE adventures) -->
                <div class="relative" v-if="hasAdventures">
                    <div class="absolute inset-0 theme-gradient-primary rounded-2xl sm:rounded-3xl blur-xl sm:blur-2xl opacity-20"></div>
                    <div class="relative theme-gradient-primary theme-text-accent rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-12 lg:p-16 max-w-5xl mx-auto overflow-hidden">
                        <!-- Decorative Circles -->
                        <div class="absolute top-0 right-0 w-32 h-32 sm:w-48 sm:h-48 lg:w-64 lg:h-64 bg-white/10 rounded-full -mr-16 sm:-mr-24 lg:-mr-32 -mt-16 sm:-mt-24 lg:-mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 sm:w-32 sm:h-32 lg:w-48 lg:h-48 bg-white/10 rounded-full -ml-12 sm:-ml-16 lg:-ml-24 -mb-12 sm:-mb-16 lg:-mb-24"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="inline-block mb-4 sm:mb-6">
                                <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-3xl sm:text-4xl lg:text-5xl" />
                            </div>
                            <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black mb-4 sm:mb-6 leading-tight px-2 sm:px-0">
                                Ready to Share Your Next Story?
                            </h3>
                            <p class="text-sm sm:text-base lg:text-lg md:text-xl opacity-95 mb-6 sm:mb-8 lg:mb-10 max-w-2xl mx-auto leading-relaxed px-2 sm:px-0">
                                Keep building your adventure log and inspire others with your journeys
                            </p>
                            <button 
                                @click="createNewAdventure"
                                class="group theme-bg-accent theme-text-primary px-6 sm:px-8 lg:px-12 py-3 sm:py-4 lg:py-5 rounded-xl sm:rounded-2xl font-bold text-sm sm:text-base lg:text-lg hover:scale-105 transition-all duration-300 shadow-lg sm:shadow-xl hover:shadow-2xl inline-flex items-center space-x-2 sm:space-x-3 lg:space-x-4 w-full sm:w-auto justify-center"
                            >
                                <FontAwesomeIcon :icon="faPlus" class="text-lg group-hover:translate-x-1 transition-transform" />
                                <span>Add New Adventure</span>
                            </button>
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
input, textarea {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

input:focus, textarea:focus {
    background: rgba(255, 255, 255, 0.2);
}
/* These will now work because CSS variables are inherited */
.theme-page {
    color: var(--text-primary);
    background-color: var(--bg-primary);
}

/* Ensure all color classes use CSS variables */
.text-primary { color: var(--color-primary) !important; }
.bg-primary { background-color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }

.text-secondary { color: var(--color-secondary) !important; }
.bg-secondary { background-color: var(--color-secondary) !important; }
.border-secondary { border-color: var(--color-secondary) !important; }

.text-accent { color: var(--color-accent) !important; }
.bg-accent { background-color: var(--color-accent) !important; }
.border-accent { border-color: var(--color-accent) !important; }
</style>