<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faEdit,
    faSave,
    faEye,
    faArrowLeft,
    faUpload,
    faImage,
    faHeading,
    faTextWidth,
    faList,
    faPalette,
    faExternalLinkAlt,
    faFileAlt,
    faDesktop,
    faCheckCircle,
    faBars,
    faTimes,
    faPlus,
    faTrash,
    faChartBar,
    faQuestionCircle, 
    faMapMarkedAlt,   
    faMobileAlt,     
    faUsers,          
    faShieldAlt,     
    faGlobe,
    faLightbulb,
    faImages,
    faSpinner 
} from '@fortawesome/free-solid-svg-icons';
import DeleteImageConfirmationDialog from '../Auth/DeleteImageConfirmationDialog.vue';

const props = defineProps({
    user: Object,
    page: String,
    pageContent: Object,
    websiteSettings: Object,
    colorPalettes: Object
});

const editingSection = ref(null);
const editedContent = reactive({ ...props.pageContent });
const imageUploading = ref(false);
const saving = ref(false);
const mobileMenuOpen = ref(false);

// Initialize sections if they don't exist
if (!editedContent.sections) {
    editedContent.sections = {};
}

// Ensure all required sections exist for home page
if (props.page === 'home') {
    if (!editedContent.sections.hero) {
        editedContent.sections.hero = {
            title: 'Explore The',
            subtitle: 'Document your journeys, share your stories, and inspire others with your adventures.'
        };
    }
    if (!editedContent.sections.features) {
        editedContent.sections.features = {
            title: 'Amazing Features',
            items: [
                {
                    title: 'Interactive Maps',
                    description: 'Track and visualize your adventures with beautiful interactive maps.'
                },
                {
                    title: 'Photo Gallery',
                    description: 'Create stunning visual stories with our easy-to-use photo gallery.'
                },
                {
                    title: 'Community Stories',
                    description: 'Connect with fellow adventurers and share experiences.'
                }
            ]
        };
    }
    if (!editedContent.sections.recent) {
        editedContent.sections.recent = {
            title: 'Recent Adventures',
            posts: []
        };
    }
    if (!editedContent.sections.mission) {
        editedContent.sections.mission = {
            title: 'Our Mission',
            content: 'We believe every adventure has a story worth telling.',
            stats: [
                { number: '0+', label: 'Adventures Logged' },
                { number: '0+', label: 'Countries Covered' },
                { number: '0+', label: 'Photos Shared' },
                { number: '1', label: 'Happy Explorer' }
            ]
        };
    }
}

// Ensure all required sections exist for about page
if (props.page === 'about') {
    if (!editedContent.sections) {
        editedContent.sections = {};
    }
    if (!editedContent.sections.hero) {
        editedContent.sections.hero = {
            title: 'About Our',
            subtitle: 'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.'
        };
    }
    if (!editedContent.sections.mission) {
        editedContent.sections.mission = {
            title: 'OUR MISSION',
            heading: 'Empowering Adventurers Worldwide',
            points: [
                'Born from a passion for exploration and storytelling, Adventure Log was created to bridge the gap between memorable experiences and lasting documentation.',
                'We understand that every journey, whether it\'s climbing mountains or exploring local hidden gems, deserves to be remembered and shared in a beautiful, meaningful way.',
                'Our platform combines intuitive design with powerful features to help you create stunning visual narratives of your adventures.'
            ],
            quote: '"Every adventure is a story waiting to be told. We\'re here to help you tell yours in the most beautiful way possible."',
            quoteAuthor: '— The Adventure Log Team'
        };
    }
    if (!editedContent.sections.featureCards) {
        editedContent.sections.featureCards = [
            {
                title: 'Global Community',
                description: 'Join adventurers from around the world sharing their incredible stories and inspiring others to explore.',
                icon: 'faGlobeAmericas'
            },
            {
                title: 'Innovative Platform',
                description: 'Cutting-edge tools and features designed specifically for documenting and sharing your adventures beautifully.',
                icon: 'faCompass'
            },
            {
                title: 'Built with Passion',
                description: 'Created by adventurers, for adventurers. We live and breathe exploration and understand your needs.',
                icon: 'faHeart'
            }
        ];
    }
    if (!editedContent.stats) {
        editedContent.stats = {
            'team_members': '5K+',
            'countries_reached': '50+',
            'years_of_passion': '3+'
        };
    }
}

// Ensure all required sections exist for contact page
if (props.page === 'contact') {
    if (!editedContent.sections) {
        editedContent.sections = {};
    }
    if (!editedContent.sections.hero) {
        editedContent.sections.hero = {
            title: 'Get In Touch',
            subtitle: "We'd love to hear about your adventures and help you share them with the world"
        };
    }
    if (!editedContent.sections.info) {
        editedContent.sections.info = {
            title: "Let's Start a Conversation",
            description: "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help."
        };
    }
    if (!editedContent.sections.social) {
        editedContent.sections.social = {
            title: 'Follow Our Adventures'
        };
    }
    if (!editedContent.sections.faq) {
        editedContent.sections.faq = {
            title: 'Frequently Asked Questions',
            description: 'Quick answers to common questions',
            items: [
                {
                    q: 'How do I start documenting my adventures?',
                    a: 'Simply create an account and start adding your first adventure story with photos and descriptions.',
                    icon: 'faMapMarkedAlt'
                },
                {
                    q: 'Is there a mobile app?',
                    a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.',
                    icon: 'faMobileAlt'
                },
                {
                    q: 'Can I collaborate with friends?',
                    a: 'Absolutely! You can create shared adventure logs with multiple contributors.',
                    icon: 'faUsers'
                },
                {
                    q: 'Is my data secure?',
                    a: 'We use enterprise-grade security to protect your stories and personal information.',
                    icon: 'faShieldAlt'
                }
            ]
        };
    }
    if (!editedContent.email) {
        editedContent.email = 'hello@example.com';
    }
    if (!editedContent.social) {
        editedContent.social = {
            instagram: '@myadventures',
            twitter: '@adventurelog',
            facebook: 'myadventurepage'
        };
    }
}

const saveContent = async () => {
    saving.value = true;
    try {
        await router.post(`/website/pages/${props.page}`, editedContent);
        setTimeout(() => saving.value = false, 1000);
    } catch (error) {
        console.error('Error saving content:', error);
        saving.value = false;
    }
};

// Add to your editor component script
const newImage = ref({
    caption: '',
    location: ''
});
const uploading = ref(false);
const fileInput = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files?.[0];

    if (!file) {
        console.warn('No file selected.');
        return;
    }

    // ✅ Validation: allowed file types
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please upload a JPG, PNG, or WEBP image.');
        console.warn('Invalid file type:', file.type);
        fileInput.value.value = ''; // reset
        return;
    }

    // ✅ Validation: max file size (5MB)
    const maxSizeMB = 5;
    if (file.size > maxSizeMB * 1024 * 1024) {
        alert(`File too large. Please upload an image smaller than ${maxSizeMB}MB.`);
        console.warn(`File size exceeded: ${file.size / 1024 / 1024}MB`);
        fileInput.value.value = ''; // reset
        return;
    }

    console.log('File selected:', file.name, 'Type:', file.type, 'Size:', file.size);
};

const uploadImage = async () => {
    const file = fileInput.value?.files?.[0];

    if (!file) {
        alert('Please select a file to upload');
        console.warn('Upload aborted: no file selected');
        return;
    }

    uploading.value = true;
    console.log('Uploading image:', file.name);

    try {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('caption', newImage.value.caption || '');
        formData.append('location', newImage.value.location || '');

        console.log('Form data prepared:', {
            caption: newImage.value.caption,
            location: newImage.value.location,
        });

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            throw new Error('CSRF token is missing. Please refresh the page.');
        }

        console.log('CSRF token found:', csrfToken ? 'Yes' : 'No');

        const response = await fetch('/gallery/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server responded with status:', response.status, errorText);
            throw new Error(`Upload failed (status ${response.status})`);
        }

        const result = await response.json();
        console.log('Upload response:', result);

        if (result.success && result.image) {
            if (!editedContent.images) {
                editedContent.images = [];
            }

            editedContent.images.push({
                ...result.image,
                id: result.firebase_key || result.image.id || Date.now().toString()
            });

            console.log('Image added to gallery:', result.image);

            // Reset form
            newImage.value = { caption: '', location: '' };
            fileInput.value.value = '';

            await saveContent();
            console.log('Gallery content saved successfully.');
        } else {
            const errorMsg = result.error || 'Unknown error during upload';
            console.error('Upload failed:', errorMsg);
            alert('Upload failed: ' + errorMsg);
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('Upload failed: ' + error.message);
    } finally {
        uploading.value = false;
        console.log('Upload process finished.');
    }
};

const updateImage = async (image) => {
    if (!image?.id) {
        console.warn('Update aborted: missing image ID.');
        return;
    }

    console.log('Updating image:', image.id);

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            throw new Error('CSRF token is missing. Please refresh the page.');
        }

        const response = await fetch(`/gallery/image/${image.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                caption: image.caption || '',
                location: image.location || ''
            }),
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Update failed, status:', response.status, errorText);
            throw new Error(`Failed to update image (status ${response.status})`);
        }

        const result = await response.json();
        console.log('Update response:', result);

        if (!result.success) {
            alert('Update failed: ' + (result.error || 'Unknown error'));
        } else {
            console.log('Image updated successfully:', image.id);
        }
    } catch (error) {
        console.error('Update error:', error);
        alert('Update failed: ' + error.message);
    }
};

// Add these new reactive variables
const showDeleteDialog = ref(false);
const imageToDelete = ref(null);
const deleting = ref(false);

// Replace the deleteImage function with this:
const openDeleteDialog = (image) => {
    imageToDelete.value = image;
    showDeleteDialog.value = true;
};

const deleteImage = async () => {
    if (!imageToDelete.value?.publitio_id || !imageToDelete.value?.id) {
        console.warn('Delete aborted: missing image identifiers.', imageToDelete.value);
        showDeleteDialog.value = false;
        return;
    }

    deleting.value = true;
    console.log('Deleting image:', imageToDelete.value.publitio_id, 'Firebase key:', imageToDelete.value.id);

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            throw new Error('CSRF token is missing. Please refresh the page.');
        }

        const response = await fetch(`/gallery/image/${imageToDelete.value.publitio_id}?firebase_key=${imageToDelete.value.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Delete request failed, status:', response.status, errorText);
            throw new Error(`Failed to delete image (status ${response.status})`);
        }

        const result = await response.json();
        console.log('Delete response:', result);

        if (result.success) {
            // Remove image from local state
            editedContent.images = editedContent.images.filter(img => img.id !== imageToDelete.value.id);
            console.log('Image removed locally:', imageToDelete.value.id);
            
            // Show success message (optional)
            // You can add a toast notification here if you have one
            
            await saveContent();
            console.log('Gallery content saved after delete.');
        } else {
            throw new Error(result.error || 'Unknown error during deletion');
        }
    } catch (error) {
        console.error('Delete error:', error);
        // Show error message to user
        alert('Delete failed: ' + error.message);
    } finally {
        deleting.value = false;
        showDeleteDialog.value = false;
        imageToDelete.value = null;
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    imageToDelete.value = null;
    deleting.value = false;
};



const startEditing = (sectionPath) => {
    editingSection.value = sectionPath;
};

const stopEditing = () => {
    editingSection.value = null;
    saveContent();
};

const addFeatureItem = () => {
    if (!editedContent.sections.features.items) {
        editedContent.sections.features.items = [];
    }
    editedContent.sections.features.items.push({
        title: 'New Feature',
        description: 'Feature description'
    });
};

const addFAQItem = () => {
    if (!editedContent.sections.faq.items) {
        editedContent.sections.faq.items = [];
    }
    editedContent.sections.faq.items.push({
        q: 'New Question?',
        a: 'Answer to the question...'
    });
};

const removeFAQItem = (index) => {
    editedContent.sections.faq.items.splice(index, 1);
};

const removeFeatureItem = (index) => {
    editedContent.sections.features.items.splice(index, 1);
};

const addMissionStat = () => {
    if (!editedContent.sections.mission.stats) {
        editedContent.sections.mission.stats = [];
    }
    editedContent.sections.mission.stats.push({
        number: '0+',
        label: 'New Stat'
    });
};

const removeMissionStat = (index) => {
    editedContent.sections.mission.stats.splice(index, 1);
};

const addMissionPoint = () => {
    if (!editedContent.sections.mission.points) {
        editedContent.sections.mission.points = [];
    }
    editedContent.sections.mission.points.push('New point...');
};

const removeMissionPoint = (index) => {
    editedContent.sections.mission.points.splice(index, 1);
};

const addFeatureCard = () => {
    if (!editedContent.sections.featureCards) {
        editedContent.sections.featureCards = [];
    }
    editedContent.sections.featureCards.push({
        title: 'New Feature',
        description: 'Description...',
        icon: 'faHeart'
    });
};

const removeFeatureCard = (index) => {
    editedContent.sections.featureCards.splice(index, 1);
};

const initializeContactPage = () => {
    editedContent.sections = {
        hero: {
            title: 'Get In Touch',
            subtitle: "We'd love to hear about your adventures and help you share them with the world"
        },
        info: {
            title: "Let's Start a Conversation",
            description: "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help."
        },
        social: {
            title: 'Follow Our Adventures'
        },
        faq: {
            title: 'Frequently Asked Questions',
            description: 'Quick answers to common questions',
            items: [
                {
                    q: 'How do I start documenting my adventures?',
                    a: 'Simply create an account and start adding your first adventure story with photos and descriptions.',
                    icon: 'faMapMarkedAlt'
                },
                {
                    q: 'Is there a mobile app?',
                    a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.',
                    icon: 'faMobileAlt'
                },
                {
                    q: 'Can I collaborate with friends?',
                    a: 'Absolutely! You can create shared adventure logs with multiple contributors.',
                    icon: 'faUsers'
                },
                {
                    q: 'Is my data secure?',
                    a: 'We use enterprise-grade security to protect your stories and personal information.',
                    icon: 'faShieldAlt'
                }
            ]
        }
    };
    editedContent.email = 'hello@example.com';
    editedContent.social = {
        instagram: '@myadventures',
        twitter: '@adventurelog',
        facebook: 'myadventurepage'
    };
    saveContent();
};

const initializeAboutPage = () => {
    editedContent.sections = {
        hero: {
            title: 'About Our',
            subtitle: 'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.'
        },
        mission: {
            title: 'OUR MISSION',
            heading: 'Empowering Adventurers Worldwide',
            points: [
                'Born from a passion for exploration and storytelling, Adventure Log was created to bridge the gap between memorable experiences and lasting documentation.',
                'We understand that every journey, whether it\'s climbing mountains or exploring local hidden gems, deserves to be remembered and shared in a beautiful, meaningful way.',
                'Our platform combines intuitive design with powerful features to help you create stunning visual narratives of your adventures.'
            ],
            quote: '"Every adventure is a story waiting to be told. We\'re here to help you tell yours in the most beautiful way possible."',
            quoteAuthor: '— The Adventure Log Team'
        },
        featureCards: [
            {
                title: 'Global Community',
                description: 'Join adventurers from around the world sharing their incredible stories and inspiring others to explore.',
                icon: 'faGlobeAmericas'
            },
            {
                title: 'Innovative Platform',
                description: 'Cutting-edge tools and features designed specifically for documenting and sharing your adventures beautifully.',
                icon: 'faCompass'
            },
            {
                title: 'Built with Passion',
                description: 'Created by adventurers, for adventurers. We live and breathe exploration and understand your needs.',
                icon: 'faHeart'
            }
        ]
    };
    editedContent.stats = {
        'team_members': '5K+',
        'countries_reached': '50+',
        'years_of_passion': '3+'
    };
    saveContent();
};

const pageTitle = computed(() => {
    return props.page.charAt(0).toUpperCase() + props.page.slice(1) + ' Page Editor';
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30">
        <!-- Mobile Menu Overlay -->
        <div 
            v-if="mobileMenuOpen" 
            class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Editor Header -->
        <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left Section -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <button 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                        >
                            <FontAwesomeIcon :icon="mobileMenuOpen ? faTimes : faBars" class="text-lg" />
                        </button>
                        
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            <FontAwesomeIcon :icon="faEdit" class="text-white text-sm sm:text-lg" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                                Editing: <span class="capitalize text-blue-600">{{ page }}</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-gray-500 truncate">Make changes and see instant preview</p>
                        </div>
                    </div>

                    <!-- Desktop Actions -->
                    <div class="hidden lg:flex items-center space-x-2 lg:space-x-3">
                        <Link 
                            :href="route('dashboard')" 
                            class="group flex items-center space-x-2 text-gray-600 hover:text-gray-900 px-3 lg:px-4 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 text-sm lg:text-base"
                        >
                            <FontAwesomeIcon :icon="faArrowLeft" class="group-hover:-translate-x-1 transition-transform text-sm" />
                            <span>Dashboard</span>
                        </Link>
                        <Link 
                            :href="route('website.page', { page })" 
                            class="group flex items-center space-x-2 text-gray-600 hover:text-blue-600 px-3 lg:px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 text-sm lg:text-base"
                            target="_blank"
                        >
                            <FontAwesomeIcon :icon="faExternalLinkAlt" class="group-hover:scale-110 transition-transform text-sm" />
                            <span>Preview</span>
                        </Link>
                    </div>

                    <!-- Mobile Save Button -->
                    <button
                        @click="saveContent"
                        :disabled="saving"
                        class="lg:hidden bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2 font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                    >
                        <FontAwesomeIcon :icon="saving ? faSpinner : faSave" class="text-sm" :class="{ 'animate-spin': saving }" />
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div 
                v-if="mobileMenuOpen" 
                class="lg:hidden absolute top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-50"
            >
                <div class="px-4 py-3 space-y-3">
                    <Link 
                        :href="route('dashboard')" 
                        class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-50 transition-colors"
                        @click="mobileMenuOpen = false"
                    >
                        <FontAwesomeIcon :icon="faArrowLeft" class="text-gray-400" />
                        <span>Back to Dashboard</span>
                    </Link>
                    <Link 
                        :href="route('website.page', { page })" 
                        class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-50 transition-colors"
                        target="_blank"
                        @click="mobileMenuOpen = false"
                    >
                        <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-gray-400" />
                        <span>Preview Page</span>
                    </Link>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto py-4 sm:py-6 px-3 sm:px-4 lg:px-8 h-[calc(100vh-4rem)] sm:h-[calc(100vh-5rem)]">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 h-full">
                <!-- Editor Panel -->
                <div class="group bg-white shadow-lg sm:shadow-xl rounded-xl sm:rounded-2xl border border-gray-100 flex flex-col h-full overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <FontAwesomeIcon :icon="faFileAlt" class="text-white text-sm sm:text-xl" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">Content Editor</h2>
                                <p class="text-xs sm:text-sm text-gray-500">Edit your page content in real-time</p>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Content Area -->
                    <div class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6 space-y-4 sm:space-y-6">
                        <!-- Page Title -->
                        <div class="p-3 sm:p-4 lg:p-5 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-blue-300 transition-all duration-300 bg-white">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center space-x-2">
                                <FontAwesomeIcon :icon="faHeading" class="text-blue-500 text-sm" />
                                <span class="text-xs sm:text-sm">Page Title</span>
                            </label>
                            <input
                                type="text"
                                v-model="editedContent.title"
                                @blur="saveContent"
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 text-base sm:text-lg font-medium"
                                placeholder="Enter your page title..."
                            />
                        </div>

                        <!-- Home Page Sections -->
                        <div v-if="page === 'home'">
                            <!-- Hero Section -->
                            <div v-if="editedContent.sections.hero" class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Hero Section</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.hero.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                                            placeholder="Enter hero title..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                        <textarea
                                            v-model="editedContent.sections.hero.subtitle"
                                            @blur="saveContent"
                                            rows="3"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                                            placeholder="Enter hero subtitle..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Features Section -->
                            <div v-if="editedContent.sections.features" class="p-3 sm:p-4 lg:p-5 border-2 border-blue-200 rounded-lg sm:rounded-xl hover:border-blue-400 transition-all duration-300 bg-blue-50/30">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <FontAwesomeIcon :icon="faList" class="text-white text-xs sm:text-sm" />
                                        </div>
                                        <span>Features Section</span>
                                    </h3>
                                    <button
                                        @click="addFeatureItem"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                                    >
                                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                                        <span>Add Feature</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.features.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                            placeholder="Enter features section title..."
                                        />
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div 
                                            v-for="(item, index) in editedContent.sections.features.items" 
                                            :key="index"
                                            class="p-3 sm:p-4 border-2 border-gray-200 rounded-lg bg-white relative"
                                        >
                                            <button
                                                @click="removeFeatureItem(index)"
                                                class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                                            >
                                                <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                                            </button>
                                            
                                            <div class="space-y-2 pr-8">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1 font-medium">Feature Title</label>
                                                    <input
                                                        type="text"
                                                        v-model="item.title"
                                                        @blur="saveContent"
                                                        class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm"
                                                        placeholder="Feature title..."
                                                    />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1 font-medium">Description</label>
                                                    <textarea
                                                        v-model="item.description"
                                                        @blur="saveContent"
                                                        rows="2"
                                                        class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm resize-vertical"
                                                        placeholder="Feature description..."
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mission Section -->
                            <div v-if="editedContent.sections.mission" class="p-3 sm:p-4 lg:p-5 border-2 border-green-200 rounded-lg sm:rounded-xl hover:border-green-400 transition-all duration-300 bg-green-50/30">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs sm:text-sm" />
                                        </div>
                                        <span>Mission Section</span>
                                    </h3>
                                    <button
                                        @click="addMissionStat"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                                    >
                                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                                        <span>Add Stat</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.mission.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                            placeholder="Enter mission title..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Content</label>
                                        <textarea
                                            v-model="editedContent.sections.mission.content"
                                            @blur="saveContent"
                                            rows="3"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-vertical"
                                            placeholder="Enter mission content..."
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Stats</label>
                                        <div class="space-y-2">
                                            <div 
                                                v-for="(stat, index) in editedContent.sections.mission.stats" 
                                                :key="index"
                                                class="p-3 border-2 border-gray-200 rounded-lg bg-white relative"
                                            >
                                                <button
                                                    @click="removeMissionStat(index)"
                                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                                                >
                                                    <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                                                </button>
                                                
                                                <div class="grid grid-cols-2 gap-2 pr-8">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1 font-medium">Number</label>
                                                        <input
                                                            type="text"
                                                            v-model="stat.number"
                                                            @blur="saveContent"
                                                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs"
                                                            placeholder="e.g., 100+"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1 font-medium">Label</label>
                                                        <input
                                                            type="text"
                                                            v-model="stat.label"
                                                            @blur="saveContent"
                                                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs"
                                                            placeholder="e.g., Adventures"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Adventures Section -->
                            <div v-if="editedContent.sections.recent" class="p-3 sm:p-4 lg:p-5 border-2 border-orange-200 rounded-lg sm:rounded-xl hover:border-orange-400 transition-all duration-300 bg-orange-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faImage" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Recent Adventures</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.recent.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                            placeholder="Enter recent adventures title..."
                                        />
                                    </div>
                                    <div class="p-3 bg-orange-100/50 rounded-lg">
                                        <p class="text-xs text-gray-600">
                                            Note: Your actual recent adventures will be displayed automatically from your adventure posts. This section title controls the heading.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About Page Sections -->
                        <div v-if="page === 'about'">
                            <!-- Initialize Button -->
                            <div v-if="!editedContent.sections.hero && !editedContent.sections.mission" class="mb-4">
                                <button
                                    @click="initializeAboutPage"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold transition-all flex items-center justify-center space-x-2"
                                >
                                    <FontAwesomeIcon :icon="faPlus" />
                                    <span>Initialize About Page Sections</span>
                                </button>
                            </div>

                            <!-- Hero Section -->
                            <div v-if="editedContent.sections.hero" class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Hero Section</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.hero.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                                            placeholder="e.g., About Our"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                        <textarea
                                            v-model="editedContent.sections.hero.subtitle"
                                            @blur="saveContent"
                                            rows="3"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                                            placeholder="Discover the story behind..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Mission Section -->
                            <div v-if="editedContent.sections.mission" class="p-3 sm:p-4 lg:p-5 border-2 border-green-200 rounded-lg sm:rounded-xl hover:border-green-400 transition-all duration-300 bg-green-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faLightbulb" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Mission Section</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.mission.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                            placeholder="e.g., OUR MISSION"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Heading</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.mission.heading"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                            placeholder="e.g., Empowering Adventurers Worldwide"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Mission Points</label>
                                        <div class="space-y-2">
                                            <div v-for="(point, index) in editedContent.sections.mission.points" :key="index" class="relative">
                                                <textarea
                                                    v-model="editedContent.sections.mission.points[index]"
                                                    @blur="saveContent"
                                                    rows="2"
                                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-vertical"
                                                    :placeholder="`Point ${index + 1}...`"
                                                />
                                                <button
                                                    v-if="editedContent.sections.mission.points.length > 1"
                                                    @click="removeMissionPoint(index)"
                                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                                                >
                                                    <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                                                </button>
                                            </div>
                                        </div>
                                        <button
                                            @click="addMissionPoint"
                                            class="mt-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center space-x-1"
                                        >
                                            <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                                            <span>Add Point</span>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Quote</label>
                                        <textarea
                                            v-model="editedContent.sections.mission.quote"
                                            @blur="saveContent"
                                            rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-vertical"
                                            placeholder="Enter inspirational quote..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Quote Author</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.mission.quoteAuthor"
                                            @blur="saveContent"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                            placeholder="e.g., — The Adventure Log Team"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Cards Section -->
                            <div v-if="editedContent.sections.featureCards" class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <FontAwesomeIcon :icon="faList" class="text-white text-xs sm:text-sm" />
                                        </div>
                                        <span>Feature Cards</span>
                                    </h3>
                                    <button
                                        @click="addFeatureCard"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                                    >
                                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                                        <span>Add Card</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div 
                                        v-for="(card, index) in editedContent.sections.featureCards" 
                                        :key="index"
                                        class="p-3 sm:p-4 border-2 border-gray-200 rounded-lg bg-white relative"
                                    >
                                        <button
                                            @click="removeFeatureCard(index)"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                                        >
                                            <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                                        </button>
                                        
                                        <div class="space-y-2 pr-8">
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1 font-medium">Card Title</label>
                                                <input
                                                    type="text"
                                                    v-model="card.title"
                                                    @blur="saveContent"
                                                    class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-xs sm:text-sm"
                                                    placeholder="Feature title..."
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1 font-medium">Description</label>
                                                <textarea
                                                    v-model="card.description"
                                                    @blur="saveContent"
                                                    rows="2"
                                                    class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-xs sm:text-sm resize-vertical"
                                                    placeholder="Feature description..."
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1 font-medium">Icon (FontAwesome name)</label>
                                                <input
                                                    type="text"
                                                    v-model="card.icon"
                                                    @blur="saveContent"
                                                    class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-xs sm:text-sm"
                                                    placeholder="e.g., faGlobeAmericas"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Section -->
                            <div v-if="editedContent.stats" class="p-3 sm:p-4 lg:p-5 border-2 border-blue-200 rounded-lg sm:rounded-xl hover:border-blue-400 transition-all duration-300 bg-blue-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Stats Section</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div v-for="(value, key) in editedContent.stats" :key="key" class="p-3 border-2 border-gray-200 rounded-lg bg-white">
                                        <label class="block text-xs text-gray-600 mb-1 font-medium capitalize">{{ key.replace('_', ' ') }}</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.stats[key]"
                                            @blur="saveContent"
                                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs"
                                            :placeholder="`e.g., 50+`"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Page Sections -->
                        <div v-if="page === 'contact'">
                            <!-- Initialize Button -->
                            <div v-if="!editedContent.sections.hero && !editedContent.sections.info" class="mb-4">
                                <button
                                    @click="initializeContactPage"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold transition-all flex items-center justify-center space-x-2"
                                >
                                    <FontAwesomeIcon :icon="faPlus" />
                                    <span>Initialize Contact Page Sections</span>
                                </button>
                            </div>

                            <!-- Hero Section -->
                            <div v-if="editedContent.sections.hero" class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Hero Section</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.hero.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                                            placeholder="Enter hero title..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                        <textarea
                                            v-model="editedContent.sections.hero.subtitle"
                                            @blur="saveContent"
                                            rows="3"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                                            placeholder="Enter hero subtitle..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div v-if="editedContent.sections.info" class="p-3 sm:p-4 lg:p-5 border-2 border-blue-200 rounded-lg sm:rounded-xl hover:border-blue-400 transition-all duration-300 bg-blue-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Contact Information</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.info.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                            placeholder="Enter section title..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea
                                            v-model="editedContent.sections.info.description"
                                            @blur="saveContent"
                                            rows="3"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-vertical"
                                            placeholder="Enter description..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input
                                            type="email"
                                            v-model="editedContent.email"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                            placeholder="Enter email address..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Section -->
                            <div v-if="editedContent.sections.social" class="p-3 sm:p-4 lg:p-5 border-2 border-green-200 rounded-lg sm:rounded-xl hover:border-green-400 transition-all duration-300 bg-green-50/30">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="faGlobe" class="text-white text-xs sm:text-sm" />
                                    </div>
                                    <span>Social Media</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Social Section Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.social.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                            placeholder="Enter social section title..."
                                        />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Instagram</label>
                                            <input
                                                type="text"
                                                v-model="editedContent.social.instagram"
                                                @blur="saveContent"
                                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                                placeholder="@username"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Twitter</label>
                                            <input
                                                type="text"
                                                v-model="editedContent.social.twitter"
                                                @blur="saveContent"
                                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                                placeholder="@username"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Facebook</label>
                                            <input
                                                type="text"
                                                v-model="editedContent.social.facebook"
                                                @blur="saveContent"
                                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                                placeholder="username"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div v-if="editedContent.sections.faq" class="p-3 sm:p-4 lg:p-5 border-2 border-orange-200 rounded-lg sm:rounded-xl hover:border-orange-400 transition-all duration-300 bg-orange-50/30">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <FontAwesomeIcon :icon="faQuestionCircle" class="text-white text-xs sm:text-sm" />
                                        </div>
                                        <span>FAQ Section</span>
                                    </h3>
                                    <button
                                        @click="addFAQItem"
                                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                                    >
                                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                                        <span>Add FAQ</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">FAQ Section Title</label>
                                        <input
                                            type="text"
                                            v-model="editedContent.sections.faq.title"
                                            @blur="saveContent"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                            placeholder="Enter FAQ section title..."
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">FAQ Description</label>
                                        <textarea
                                            v-model="editedContent.sections.faq.description"
                                            @blur="saveContent"
                                            rows="2"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm resize-vertical"
                                            placeholder="Enter FAQ description..."
                                        />
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div 
                                            v-for="(faq, index) in editedContent.sections.faq.items" 
                                            :key="index"
                                            class="p-3 sm:p-4 border-2 border-gray-200 rounded-lg bg-white relative"
                                        >
                                            <button
                                                @click="removeFAQItem(index)"
                                                class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                                            >
                                                <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                                            </button>
                                            
                                            <div class="space-y-2 pr-8">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1 font-medium">Question</label>
                                                    <input
                                                        type="text"
                                                        v-model="faq.q"
                                                        @blur="saveContent"
                                                        class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-xs sm:text-sm"
                                                        placeholder="Enter question..."
                                                    />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1 font-medium">Answer</label>
                                                    <textarea
                                                        v-model="faq.a"
                                                        @blur="saveContent"
                                                        rows="2"
                                                        class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-xs sm:text-sm resize-vertical"
                                                        placeholder="Enter answer..."
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


<!-- Gallery Editor Section -->
<div v-if="page === 'gallery'" class="p-4 sm:p-6 border-2 border-purple-200 rounded-xl bg-purple-50/30">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center space-x-2">
        <FontAwesomeIcon :icon="faImages" class="text-purple-600" />
        <span>Gallery Images Management</span>
    </h3>
    
    <!-- Upload Section -->
    <div class="mb-6 p-4 bg-white rounded-lg border-2 border-dashed border-gray-300">
        <h4 class="font-semibold text-gray-800 mb-3">Upload New Image</h4>
        <div class="space-y-3">
            <input
                type="file"
                ref="fileInput"
                @change="handleFileUpload"
                accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
            />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input
                    v-model="newImage.caption"
                    type="text"
                    placeholder="Image caption"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                />
                <input
                    v-model="newImage.location"
                    type="text"
                    placeholder="Location"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                />
            </div>
            <button
                @click="uploadImage"
                :disabled="uploading"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 flex items-center space-x-2"
            >
                <FontAwesomeIcon :icon="uploading ? faSpinner : faUpload" :class="{ 'animate-spin': uploading }" />
                <span>{{ uploading ? 'Uploading...' : 'Upload Image' }}</span>
            </button>
        </div>
    </div>

    <!-- Existing Images -->
<div class="space-y-4">
    <h4 class="font-semibold text-gray-800">Existing Images ({{ editedContent.images?.length || 0 }})</h4>
    
    <!-- No Images State -->
    <div v-if="!editedContent.images?.length" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <FontAwesomeIcon :icon="faImages" class="text-purple-500 text-2xl" />
        </div>
        <h5 class="text-lg font-semibold text-gray-700 mb-2">No Images Uploaded Yet</h5>
        <p class="text-gray-500 text-sm max-w-md mx-auto">
            Upload your first adventure photo to start building your gallery. 
            Images will appear here once uploaded.
        </p>
    </div>

    <!-- Images Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div 
            v-for="(image, index) in editedContent.images" 
            :key="image.id"
            class="bg-white rounded-lg border border-gray-200 p-3"
        >
            <img :src="image.url" :alt="image.caption" class="w-full h-32 object-cover rounded mb-2" />
            <input
                v-model="image.caption"
                @blur="updateImage(image)"
                type="text"
                class="w-full px-2 py-1 border border-gray-300 rounded text-sm mb-2"
                placeholder="Caption"
            />
            <input
                v-model="image.location"
                @blur="updateImage(image)"
                type="text"
                class="w-full px-2 py-1 border border-gray-300 rounded text-sm mb-2"
                placeholder="Location"
            />
           <button
                        @click="openDeleteDialog(image)"
                        class="w-full bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600 flex items-center justify-center space-x-1 transition-all duration-200"
                    >
                        <FontAwesomeIcon :icon="faTrash" />
                        <span>Delete</span>
                    </button>
        </div>
    </div>
</div>
</div>
                        <!-- Simple Content Editor (for other pages) -->
                        <div v-if="page !== 'home' && page !== 'about' && page !== 'contact'" class="p-3 sm:p-4 lg:p-5 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-blue-300 transition-all duration-300 bg-white">
                            <div class="mb-3 sm:mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center space-x-2">
                                    <FontAwesomeIcon :icon="faTextWidth" class="text-blue-500 text-sm" />
                                    <span class="text-xs sm:text-sm">Page Content</span>
                                </label>
                                <textarea
                                    v-model="editedContent.content"
                                    @blur="saveContent"
                                    rows="6"
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-vertical text-sm"
                                    placeholder="Enter your page content..."
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="p-3 sm:p-4 lg:p-6 border-t border-gray-200 bg-gray-50/50 flex-shrink-0">
                        <button
                            @click="saveContent"
                            :disabled="saving"
                            class="group w-full bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl transition-all duration-300 hover:shadow-xl hover:scale-105 flex items-center justify-center space-x-2 sm:space-x-3 font-bold text-sm sm:text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <FontAwesomeIcon :icon="saving ? faSpinner : faCheckCircle" class="text-lg sm:text-xl group-hover:scale-110 transition-transform" :class="{ 'animate-spin': saving }" />
                            <span>{{ saving ? 'Saving...' : 'Save All Changes' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Live Preview Panel -->
                <div class="group bg-white shadow-lg sm:shadow-xl rounded-xl sm:rounded-2xl border border-gray-100 flex flex-col h-full overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <FontAwesomeIcon :icon="faDesktop" class="text-white text-sm sm:text-xl" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">Live Preview</h2>
                                <p class="text-xs sm:text-sm text-gray-500">Real-time preview of your changes</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scrollable Preview Content -->
                    <div class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 sm:p-6 bg-gradient-to-br from-gray-50 to-blue-50/30 hover:border-blue-400 transition-all duration-300 h-full flex flex-col">
                            <div class="text-center mb-4 sm:mb-6">
                                <FontAwesomeIcon :icon="faEye" class="text-blue-500 text-2xl sm:text-3xl mb-2 sm:mb-3" />
                                <p class="text-gray-600 text-xs sm:text-sm">
                                    Changes auto-save as you type. Preview updates in real-time.
                                </p>
                            </div>
                            
                            <!-- Mini Preview -->
                            <div class="bg-white border-2 border-gray-200 rounded-xl p-3 sm:p-4 lg:p-5 shadow-lg hover:shadow-xl transition-all duration-300 mb-4 sm:mb-6 flex-1 overflow-y-auto">
                                <h3 class="text-lg sm:text-xl font-bold text-blue-600 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <FontAwesomeIcon :icon="faFileAlt" class="text-blue-500 text-sm" />
                                    <span class="truncate">{{ editedContent.title || 'Page Title' }}</span>
                                </h3>
                                
                                <!-- Home Page Preview -->
                                <div v-if="page === 'home'" class="space-y-3 sm:space-y-4">
                                    <!-- Hero Preview -->
                                    <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                                        <h4 class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                                            </div>
                                            <span>Hero Section</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.hero?.title }}</p>
                                        <p class="text-xs text-gray-600">{{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...</p>
                                    </div>

                                    <!-- Features Preview -->
                                    <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                        <h4 class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faList" class="text-white text-xs" />
                                            </div>
                                            <span>Features ({{ editedContent.sections.features?.items?.length || 0 }})</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-2">{{ editedContent.sections.features?.title }}</p>
                                        <div class="space-y-1">
                                            <div v-for="(item, i) in editedContent.sections.features?.items?.slice(0, 2)" :key="i" class="text-xs text-gray-600">
                                                • {{ item.title }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mission Preview -->
                                    <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                        <h4 class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs" />
                                            </div>
                                            <span>Mission & Stats ({{ editedContent.sections.mission?.stats?.length || 0 }})</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.mission?.title }}</p>
                                        <p class="text-xs text-gray-600 mb-2">{{ editedContent.sections.mission?.content?.substring(0, 60) }}...</p>
                                        <div class="grid grid-cols-2 gap-1">
                                            <div v-for="(stat, i) in editedContent.sections.mission?.stats?.slice(0, 4)" :key="i" class="text-xs bg-white rounded p-1.5">
                                                <span class="font-bold text-green-600">{{ stat.number }}</span>
                                                <span class="text-gray-600 text-xs"> {{ stat.label }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Adventures Preview -->
                                    <div class="p-3 sm:p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                                        <h4 class="font-bold text-orange-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-orange-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faImage" class="text-white text-xs" />
                                            </div>
                                            <span>{{ editedContent.sections.recent?.title }}</span>
                                        </h4>
                                        <p class="text-xs text-gray-600">Dynamic - shows your actual adventures</p>
                                    </div>
                                </div>

                                <!-- About Page Preview -->
                                <div v-else-if="page === 'about'" class="space-y-3 sm:space-y-4">
                                    <!-- Hero Preview -->
                                    <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                                        <h4 class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                                            </div>
                                            <span>Hero Section</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.hero?.title }}</p>
                                        <p class="text-xs text-gray-600">{{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...</p>
                                    </div>

                                    <!-- Mission Preview -->
                                    <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                        <h4 class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faLightbulb" class="text-white text-xs" />
                                            </div>
                                            <span>Mission Section</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.mission?.title }}</p>
                                        <p class="text-xs text-gray-600">{{ editedContent.sections.mission?.heading?.substring(0, 60) }}...</p>
                                        <div class="space-y-1 mt-2">
                                            <div v-for="(point, i) in editedContent.sections.mission?.points?.slice(0, 2)" :key="i" class="text-xs text-gray-600">
                                                • {{ point.substring(0, 50) }}...
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Feature Cards Preview -->
                                    <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                                        <h4 class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faList" class="text-white text-xs" />
                                            </div>
                                            <span>Feature Cards ({{ editedContent.sections.featureCards?.length || 0 }})</span>
                                        </h4>
                                        <div class="space-y-1">
                                            <div v-for="(card, i) in editedContent.sections.featureCards?.slice(0, 2)" :key="i" class="text-xs text-gray-600">
                                                • {{ card.title }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stats Preview -->
                                    <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                        <h4 class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs" />
                                            </div>
                                            <span>Stats Section</span>
                                        </h4>
                                        <div class="grid grid-cols-2 gap-1">
                                            <div v-for="(value, key) in editedContent.stats" :key="key" class="text-xs bg-white rounded p-1.5">
                                                <span class="font-bold text-blue-600">{{ value }}</span>
                                                <span class="text-gray-600 text-xs"> {{ key.replace('_', ' ') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Page Preview -->
                                <div v-else-if="page === 'contact'" class="space-y-3 sm:space-y-4">
                                    <!-- Hero Preview -->
                                    <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                                        <h4 class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                                            </div>
                                            <span>Hero Section</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.hero?.title }}</p>
                                        <p class="text-xs text-gray-600">{{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...</p>
                                    </div>

                                    <!-- Contact Info Preview -->
                                    <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                        <h4 class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-white text-xs" />
                                            </div>
                                            <span>Contact Information</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.info?.title }}</p>
                                        <p class="text-xs text-gray-600">{{ editedContent.sections.info?.description?.substring(0, 60) }}...</p>
                                        <p class="text-xs text-blue-600 mt-1">{{ editedContent.email }}</p>
                                    </div>

                                    <!-- Social Media Preview -->
                                    <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                        <h4 class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faGlobe" class="text-white text-xs" />
                                            </div>
                                            <span>Social Media</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.social?.title }}</p>
                                        <div class="space-y-1">
                                            <div v-if="editedContent.social?.instagram" class="text-xs text-gray-600">
                                                • Instagram: {{ editedContent.social.instagram }}
                                            </div>
                                            <div v-if="editedContent.social?.twitter" class="text-xs text-gray-600">
                                                • Twitter: {{ editedContent.social.twitter }}
                                            </div>
                                            <div v-if="editedContent.social?.facebook" class="text-xs text-gray-600">
                                                • Facebook: {{ editedContent.social.facebook }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FAQ Preview -->
                                    <div class="p-3 sm:p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                                        <h4 class="font-bold text-orange-900 text-xs sm:text-sm mb-2 flex items-center space-x-2">
                                            <div class="w-4 h-4 bg-orange-600 rounded flex items-center justify-center">
                                                <FontAwesomeIcon :icon="faQuestionCircle" class="text-white text-xs" />
                                            </div>
                                            <span>FAQ Section</span>
                                        </h4>
                                        <p class="text-xs text-gray-700 font-semibold mb-1">{{ editedContent.sections.faq?.title }}</p>
                                        <p class="text-xs text-gray-600 mb-2">{{ editedContent.sections.faq?.description }}</p>
                                        <div class="space-y-1">
                                            <div v-for="(faq, i) in editedContent.sections.faq?.items?.slice(0, 2)" :key="i" class="text-xs text-gray-600">
                                                • {{ faq.q }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Pages Preview -->
                                <div v-else class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                                    <p class="text-gray-600 text-xs sm:text-sm">{{ editedContent.content?.substring(0, 150) || 'No content yet...' }}</p>
                                </div>
                            </div>

                            <!-- Quick Preview Link -->
                            <div class="text-center mt-auto">
                                <Link 
                                    :href="route('website.page', { page })" 
                                    target="_blank"
                                    class="group inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105 font-semibold text-sm sm:text-base w-full sm:w-auto justify-center"
                                >
                                    <FontAwesomeIcon :icon="faExternalLinkAlt" class="group-hover:scale-110 transition-transform text-sm" />
                                    <span>Open Full Preview</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       <!-- Add the delete confirmation dialog at the end of your template -->
    <DeleteImageConfirmationDialog
        :show="showDeleteDialog"
        :image="imageToDelete"
        :deleting="deleting"
        @confirm="deleteImage"
        @cancel="cancelDelete"
    />
</template>

<style scoped>
/* Custom scrollbar styling */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@media (min-width: 640px) {
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
}
</style>