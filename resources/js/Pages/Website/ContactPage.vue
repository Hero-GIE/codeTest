<script setup>
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
    faEnvelope, 
    faPaperPlane, 
    faComments,
    faQuestionCircle,
    faShieldAlt,
    faMobileAlt,
    faUsers,
    faMapMarkedAlt,
    faStar,
    faCheckCircle,
    faPhone,
    faGlobe,
    faArrowDown,
    faUser,
    faSpinner
} from '@fortawesome/free-solid-svg-icons';
import {
    faInstagram,
    faTwitter,
    faFacebook
} from '@fortawesome/free-brands-svg-icons';

defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean
});

const form = ref({
    name: '',
    email: '',
    message: ''
});

const loading = ref(false);

const submitForm = async () => {
    loading.value = true;
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    alert('Thank you for your message! We\'ll get back to you soon.');
    form.value = { name: '', email: '', message: '' };
    loading.value = false;
};

const getFAQIcon = (iconName) => {
    const iconMap = {
        'faMapMarkedAlt': faMapMarkedAlt,
        'faMobileAlt': faMobileAlt,
        'faUsers': faUsers,
        'faShieldAlt': faShieldAlt
    };
    return iconMap[iconName] || faQuestionCircle;
};
</script>

<template>
    <div class="overflow-hidden">
        <!-- Compact Hero Section -->
        <section class="relative h-[40vh] min-h-[300px] sm:min-h-[350px] max-h-[500px] overflow-hidden">
            <!-- Background with Gradient -->
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-primary"></div>
                
                <!-- Overlay Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 50px 50px;"></div>
                </div>
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/40"></div>

                <!-- Floating Icons -->
                <div class="absolute top-8 sm:top-16 left-4 sm:left-10 text-accent/20 text-3xl sm:text-5xl animate-float">
                    <FontAwesomeIcon :icon="faEnvelope" />
                </div>
                <div class="absolute bottom-8 sm:bottom-16 right-8 sm:right-16 text-accent/20 text-2xl sm:text-4xl animate-float-delayed">
                    <FontAwesomeIcon :icon="faComments" />
                </div>
            </div>

            <!-- Hero Content -->
            <div 
                class="relative h-full flex items-center justify-center bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1596524430615-b46475ddff6e?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170');"
            >
                <div class="absolute inset-0 bg-black/50"></div>

                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <!-- Badge -->
                    <div class="mb-6 sm:mb-8 inline-block animate-fade-in-down">
                        <span class="bg-white/10 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full text-xs sm:text-sm font-semibold backdrop-blur-md border border-white/30 inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faComments" />
                            <span>Let's Connect</span>
                        </span>
                    </div>
           
                    <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-3 sm:mb-4 leading-tight animate-fade-in-up">
                        {{ pageContent?.sections?.hero?.title || 'Get In Touch' }}
                    </h1>
                    <p class="text-sm sm:text-lg md:text-xl text-white max-w-3xl mx-auto leading-relaxed animate-fade-in-up delay-200">
                        {{ pageContent?.sections?.hero?.subtitle || "We'd love to hear about your adventures and help you share them with the world" }}
                    </p>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-4 sm:bottom-6 left-1/2 transform -translate-x-1/2 animate-bounce">
                <FontAwesomeIcon :icon="faArrowDown" class="text-white text-xl sm:text-2xl" />
            </div>
        </section>

        <!-- Contact Content -->
        <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-b from-accent to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                    <!-- Contact Information -->
                    <div class="space-y-6 sm:space-y-8">
                        <!-- Header -->
                        <div>
                            <div class="inline-flex items-center space-x-2 bg-primary/10 text-primary px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold mb-4 sm:mb-6">
                                <FontAwesomeIcon :icon="faMapMarkedAlt" />
                                <span>CONTACT INFO</span>
                            </div>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-primary mb-4 sm:mb-6 leading-tight">
                                {{ pageContent?.sections?.info?.title || "Let's Start a Conversation" }}
                            </h2>
                            <p class="text-gray-600 text-sm sm:text-base lg:text-lg leading-relaxed">
                                {{ pageContent?.sections?.info?.description || "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help." }}
                            </p>
                        </div>

                        <!-- Contact Cards -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Email Card -->
                            <div v-if="pageContent?.email" class="group relative bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-lg sm:hover:shadow-xl overflow-hidden">
                                <div class="absolute top-0 right-0 w-16 h-16 sm:w-24 sm:h-24 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full"></div>
                                <div class="flex items-start space-x-3 sm:space-x-5">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-primary to-secondary rounded-lg sm:rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-105 sm:group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon :icon="faEnvelope" class="text-lg sm:text-xl lg:text-2xl" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-primary text-lg sm:text-xl mb-1 sm:mb-2 group-hover:text-secondary transition-colors truncate">Email Us</h3>
                                        <p class="text-gray-700 font-medium text-sm sm:text-base lg:text-lg mb-1 truncate">{{ pageContent.email }}</p>
                                        <p class="text-gray-500 text-xs sm:text-sm flex items-center space-x-1 sm:space-x-2">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500 text-xs" />
                                            <span>We'll respond within 24 hours</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone Card -->
                            <div class="group relative bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-lg sm:hover:shadow-xl overflow-hidden">
                                <div class="absolute top-0 right-0 w-16 h-16 sm:w-24 sm:h-24 bg-gradient-to-br from-secondary/5 to-transparent rounded-bl-full"></div>
                                <div class="flex items-start space-x-3 sm:space-x-5">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-secondary to-primary rounded-lg sm:rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-105 sm:group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon :icon="faPhone" class="text-lg sm:text-xl lg:text-2xl" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-primary text-lg sm:text-xl mb-1 sm:mb-2 group-hover:text-secondary transition-colors truncate">Live Support</h3>
                                        <p class="text-gray-700 font-medium text-sm sm:text-base lg:text-lg mb-1">Available 24/7</p>
                                        <p class="text-gray-500 text-xs sm:text-sm flex items-center space-x-1 sm:space-x-2">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500 text-xs" />
                                            <span>Real-time assistance</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Section -->
                            <div v-if="pageContent?.social" class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border-2 border-gray-100">
                                <h3 class="font-bold text-primary text-lg sm:text-xl mb-4 sm:mb-6 flex items-center space-x-2">
                                    <FontAwesomeIcon :icon="faGlobe" />
                                    <span class="truncate">{{ pageContent?.sections?.social?.title || 'Follow Our Adventures' }}</span>
                                </h3>
                                <div class="grid grid-cols-1 xs:grid-cols-2 gap-3 sm:gap-4">
                                    <div 
                                        v-for="(value, platform) in pageContent.social" 
                                        :key="platform"
                                        class="group flex items-center space-x-3 sm:space-x-4 p-3 sm:p-4 bg-gray-50 rounded-lg sm:rounded-xl hover:bg-gradient-to-br hover:from-primary/10 hover:to-secondary/10 transition-all duration-300 cursor-pointer"
                                    >
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-accent group-hover:scale-105 sm:group-hover:scale-110 transition-transform flex-shrink-0">
                                            <FontAwesomeIcon 
                                                :icon="platform === 'instagram' ? faInstagram : platform === 'twitter' ? faTwitter : faFacebook" 
                                                class="text-sm sm:text-base"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-primary capitalize text-xs sm:text-sm truncate">{{ platform }}</p>
                                            <p class="text-gray-600 text-xs truncate">{{ value }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Support Card -->
                            <div class="relative overflow-hidden rounded-xl sm:rounded-2xl">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-primary"></div>
                                <div class="relative p-4 sm:p-6 text-accent">
                                    <div class="flex items-start space-x-3 sm:space-x-4">
                                        <FontAwesomeIcon :icon="faQuestionCircle" class="text-xl sm:text-2xl lg:text-3xl flex-shrink-0 mt-1" />
                                        <div class="flex-1">
                                            <h3 class="font-bold text-lg sm:text-xl mb-1 sm:mb-2">Quick Support</h3>
                                            <p class="opacity-95 leading-relaxed text-sm sm:text-base">Need immediate help? Check our FAQ section for quick answers to common questions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form - Login Style -->
                    <div class="lg:sticky lg:top-8 h-fit">
                        <div class="relative">
                            <!-- Glow Effect -->
                            <div class="absolute -inset-2 sm:-inset-1 bg-gradient-to-br from-primary via-secondary to-primary rounded-2xl sm:rounded-3xl blur-lg sm:blur-xl opacity-20"></div>
                            
                            <!-- Form Card -->
                            <div class="relative bg-white rounded-2xl sm:rounded-3xl shadow-lg sm:shadow-2xl p-6 sm:p-8 lg:p-10 border border-gray-100">
                                <!-- Header -->
                                <div class="text-center mb-6 sm:mb-8">
                                    <div class="flex justify-center mb-3 sm:mb-4">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-black to-gray-800 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg shadow-black/30 animate-float">
                                            <FontAwesomeIcon :icon="faPaperPlane" class="text-white text-lg sm:text-xl" />
                                        </div>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-black to-gray-800 bg-clip-text text-transparent mb-2">
                                        Send Message
                                    </h3>
                                    <p class="text-gray-600 text-sm sm:text-base">We'd love to hear from you!</p>
                                </div>

                                <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-6">
                                    <!-- Name Field -->
                                    <div class="group">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">
                                            Your Name *
                                        </label>
                                        <div class="relative">
                                            <input
                                                type="text"
                                                v-model="form.name"
                                                required
                                                class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-white border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-md sm:group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500 text-sm sm:text-base"
                                                placeholder="John Doe"
                                            />
                                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none mt-1">
                                                <FontAwesomeIcon :icon="faUser" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="group">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">
                                            Email Address *
                                        </label>
                                        <div class="relative">
                                            <input
                                                type="email"
                                                v-model="form.email"
                                                required
                                                class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-white border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-md sm:group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500 text-sm sm:text-base"
                                                placeholder="john@example.com"
                                            />
                                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none mt-1">
                                                <FontAwesomeIcon :icon="faEnvelope" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Field -->
                                    <div class="group">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">
                                            Your Message *
                                        </label>
                                        <div class="relative">
                                            <textarea
                                                v-model="form.message"
                                                required
                                                rows="4"
                                                class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-white border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-md sm:group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500 text-sm sm:text-base resize-none"
                                                placeholder="Tell us about your adventure or how we can help..."
                                            ></textarea>
                                            <div class="absolute top-3 sm:top-4 left-3 sm:left-4 pointer-events-none">
                                                <FontAwesomeIcon :icon="faComments" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-3 sm:pt-4">
                                        <button
                                            type="submit"
                                            :disabled="loading"
                                            class="w-full flex items-center justify-center gap-2 sm:gap-3 py-3 sm:py-4 px-4 sm:px-6 bg-gradient-to-r from-black to-gray-800 hover:from-gray-800 hover:to-gray-900 focus:ring-2 focus:ring-black focus:ring-offset-2 focus:ring-offset-white text-white font-semibold rounded-lg sm:rounded-xl shadow-lg shadow-black/25 hover:shadow-xl hover:shadow-black/30 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] border border-gray-800/20 text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <FontAwesomeIcon 
                                                v-if="loading" 
                                                :icon="faSpinner" 
                                                class="animate-spin h-4 w-4 sm:h-5 sm:w-5" 
                                            />
                                            <FontAwesomeIcon 
                                                v-else 
                                                :icon="faPaperPlane" 
                                                class="h-4 w-4 sm:h-5 sm:w-5" 
                                            />
                                            {{ loading ? 'Sending...' : 'Send Message' }}
                                        </button>
                                    </div>

                                    <p class="text-gray-500 text-xs sm:text-sm text-center flex items-center justify-center space-x-1 sm:space-x-2">
                                        <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500 text-xs" />
                                        <span>We typically respond within 24 hours</span>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-b from-gray-50 to-accent">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12 sm:mb-16">
                    <div class="inline-block mb-4 sm:mb-6">
                        <span class="bg-primary/10 text-primary px-4 sm:px-5 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold inline-flex items-center space-x-1 sm:space-x-2">
                            <FontAwesomeIcon :icon="faQuestionCircle" />
                            <span>FAQ</span>
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-primary mb-3 sm:mb-4">
                        {{ pageContent?.sections?.faq?.title || 'Frequently Asked Questions' }}
                    </h2>
                    <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto">
                        {{ pageContent?.sections?.faq?.description || 'Quick answers to common questions' }}
                    </p>
                </div>

                <!-- FAQ Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                    <div 
                        v-for="(faq, index) in pageContent?.sections?.faq?.items || [
                            { q: 'How do I start documenting my adventures?', a: 'Simply create an account and start adding your first adventure story with photos and descriptions.', icon: faMapMarkedAlt },
                            { q: 'Is there a mobile app?', a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.', icon: faMobileAlt },
                            { q: 'Can I collaborate with friends?', a: 'Absolutely! You can create shared adventure logs with multiple contributors.', icon: faUsers },
                            { q: 'Is my data secure?', a: 'We use enterprise-grade security to protect your stories and personal information.', icon: faShieldAlt }
                        ]" 
                        :key="faq.q"
                        class="group bg-white p-6 sm:p-8 rounded-xl sm:rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-lg sm:hover:shadow-xl"
                    >
                        <div class="flex items-start space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-primary to-secondary rounded-lg sm:rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-105 sm:group-hover:scale-110 transition-transform">
                                <FontAwesomeIcon :icon="getFAQIcon(faq.icon)" class="text-base sm:text-lg" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-primary text-base sm:text-lg mb-2 sm:mb-3 group-hover:text-secondary transition-colors">
                                    {{ faq.q }}
                                </h3>
                                <p class="text-gray-600 leading-relaxed text-sm sm:text-base">{{ faq.a }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Help CTA -->
                <div class="mt-8 sm:mt-12 text-center">
                    <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">Still have questions?</p>
                    <button class="inline-flex items-center space-x-2 bg-primary text-accent px-6 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg text-sm sm:text-base">
                        <FontAwesomeIcon :icon="faComments" />
                        <span>Contact Support</span>
                    </button>
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
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-10px) rotate(3deg);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-15px) rotate(-3deg);
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

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
}

.animate-bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0) translateX(-50%);
    }
    50% {
        transform: translateY(-8px) translateX(-50%);
    }
}

/* Custom breakpoint for extra small screens */
@media (min-width: 475px) {
    .xs\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .hover\:scale-\[1\.02\]:hover {
        transform: scale(1);
    }
    
    .group-hover\:scale-110:hover {
        transform: scale(1);
    }
}

/* Prevent zoom on iOS for inputs */
@media (max-width: 768px) {
    input, select, textarea {
        font-size: 16px !important;
    }
}
</style>