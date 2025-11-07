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
    faArrowDown
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

const submitForm = () => {
    // Handle form submission
    alert('Thank you for your message! We\'ll get back to you soon.');
    form.value = { name: '', email: '', message: '' };
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
        <!-- Compact Hero Section - Quarter Height -->
        <section class="relative h-[40vh] min-h-[350px] max-h-[450px] overflow-hidden">
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
                <div class="absolute top-16 left-10 text-accent/20 text-5xl animate-float">
                    <FontAwesomeIcon :icon="faEnvelope" />
                </div>
                <div class="absolute bottom-16 right-16 text-accent/20 text-4xl animate-float-delayed">
                    <FontAwesomeIcon :icon="faComments" />
                </div>
            </div>

            <!-- Hero Content -->
          <div 
  class="relative h-full flex items-center justify-center bg-cover bg-center"
  style="background-image: url('https://images.unsplash.com/photo-1596524430615-b46475ddff6e?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170');"
>
    <!-- Optional dark overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <!-- Badge -->
           <div class="mb-8 inline-block animate-fade-in-down">
                    <span class="bg-white/10 text-white px-6 py-3 rounded-full text-sm font-semibold backdrop-blur-md border border-white/30 inline-flex items-center space-x-2">
                        <FontAwesomeIcon :icon="faComments" />
                        <span>Let's Connect</span>
                    </span>
                </div>
       
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-4 leading-tight animate-fade-in-up">
            {{ pageContent?.sections?.hero?.title || 'Get In Touch' }}
        </h1>
        <p class="text-lg md:text-xl text-white max-w-3xl mx-auto leading-relaxed animate-fade-in-up delay-200">
            {{ pageContent?.sections?.hero?.subtitle || "We'd love to hear about your adventures and help you share them with the world" }}
        </p>
    </div>
</div>


            <!-- Scroll Indicator -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 animate-bounce">
                <FontAwesomeIcon :icon="faArrowDown" class="text-white text-2xl" />
            </div>
        </section>

        <!-- Contact Content -->
        <section class="py-20 bg-gradient-to-b from-accent to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <!-- Header -->
                        <div>
                            <div class="inline-flex items-center space-x-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-bold mb-6">
                                <FontAwesomeIcon :icon="faMapMarkedAlt" />
                                <span>CONTACT INFO</span>
                            </div>
                            <h2 class="text-4xl md:text-5xl font-black text-primary mb-6 leading-tight">
                              {{ pageContent?.sections?.info?.title || "Let's Start a Conversation" }}
                            </h2>
                           <p class="text-gray-600 text-lg leading-relaxed">
    {{ pageContent?.sections?.info?.description || "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help." }}
</p>
                        </div>

                        <!-- Contact Cards -->
                        <div class="space-y-6">
                            <!-- Email Card -->
                            <div v-if="pageContent?.email" class="group relative bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-xl overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full"></div>
                                <div class="flex items-start space-x-5">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon :icon="faEnvelope" class="text-2xl" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-primary text-xl mb-2 group-hover:text-secondary transition-colors">Email Us</h3>
                                        <p class="text-gray-700 font-medium text-lg mb-1">{{ pageContent.email }}</p>
                                        <p class="text-gray-500 text-sm flex items-center space-x-2">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500" />
                                            <span>We'll respond within 24 hours</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone Card (Optional) -->
                            <div class="group relative bg-white p-6 rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-xl overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-secondary/5 to-transparent rounded-bl-full"></div>
                                <div class="flex items-start space-x-5">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-gradient-to-br from-secondary to-primary rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon :icon="faPhone" class="text-2xl" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-primary text-xl mb-2 group-hover:text-secondary transition-colors">Live Support</h3>
                                        <p class="text-gray-700 font-medium text-lg mb-1">Available 24/7</p>
                                        <p class="text-gray-500 text-sm flex items-center space-x-2">
                                            <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500" />
                                            <span>Real-time assistance</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Section -->
                            <div v-if="pageContent?.social" class="bg-white p-6 rounded-2xl border-2 border-gray-100">
                                <h3 class="font-bold text-primary text-xl mb-6 flex items-center space-x-2">
                                    <FontAwesomeIcon :icon="faGlobe" />
                                     <span>{{ pageContent?.sections?.social?.title || 'Follow Our Adventures' }}</span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div 
                                        v-for="(value, platform) in pageContent.social" 
                                        :key="platform"
                                        class="group flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gradient-to-br hover:from-primary/10 hover:to-secondary/10 transition-all duration-300 cursor-pointer"
                                    >
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-accent group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon 
                                                :icon="platform === 'instagram' ? faInstagram : platform === 'twitter' ? faTwitter : faFacebook" 
                                                class="text-lg"
                                            />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-primary capitalize text-sm">{{ platform }}</p>
                                            <p class="text-gray-600 text-xs">{{ value }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Support Card -->
                            <div class="relative overflow-hidden rounded-2xl">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-primary"></div>
                                <div class="relative p-6 text-accent">
                                    <div class="flex items-start space-x-4">
                                        <FontAwesomeIcon :icon="faQuestionCircle" class="text-3xl flex-shrink-0 mt-1" />
                                        <div>
                                            <h3 class="font-bold text-xl mb-2">Quick Support</h3>
                                            <p class="opacity-95 leading-relaxed">Need immediate help? Check our FAQ section for quick answers to common questions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:sticky lg:top-8 h-fit">
                        <div class="relative">
                            <!-- Glow Effect -->
                            <div class="absolute -inset-1 bg-gradient-to-br from-primary via-secondary to-primary rounded-3xl blur-xl opacity-20"></div>
                            
                            <!-- Form Card -->
                            <div class="relative bg-white rounded-3xl shadow-2xl p-8 lg:p-10 border border-gray-100">
                                <div class="text-center mb-8">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl mb-4 text-accent shadow-lg">
                                        <FontAwesomeIcon :icon="faPaperPlane" class="text-2xl" />
                                    </div>
                                    <h3 class="text-3xl font-black text-primary">Send us a Message</h3>
                                    <p class="text-gray-600 mt-2">We'd love to hear from you!</p>
                                </div>

                                <form @submit.prevent="submitForm" class="space-y-6">
                                    <!-- Name Field -->
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Your Name *
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.name"
                                            required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-gray-900 placeholder-gray-400"
                                            placeholder="John Doe"
                                        >
                                    </div>

                                    <!-- Email Field -->
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Email Address *
                                        </label>
                                        <input
                                            type="email"
                                            v-model="form.email"
                                            required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-gray-900 placeholder-gray-400"
                                            placeholder="john@example.com"
                                        >
                                    </div>

                                    <!-- Message Field -->
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Your Message *
                                        </label>
                                        <textarea
                                            v-model="form.message"
                                            required
                                            rows="5"
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 resize-none text-gray-900 placeholder-gray-400"
                                            placeholder="Tell us about your adventure or how we can help..."
                                        ></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button
                                        type="submit"
                                        class="group w-full bg-gradient-to-r from-primary to-secondary text-accent py-5 rounded-xl font-black text-lg hover:scale-105 hover:shadow-2xl transition-all duration-300 shadow-lg flex items-center justify-center space-x-3"
                                    >
                                        <FontAwesomeIcon :icon="faPaperPlane" class="group-hover:translate-x-1 transition-transform" />
                                        <span>Send Message</span>
                                    </button>

                                    <p class="text-gray-500 text-sm text-center flex items-center justify-center space-x-2">
                                        <FontAwesomeIcon :icon="faCheckCircle" class="text-green-500" />
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
        <section class="py-20 bg-gradient-to-b from-gray-50 to-accent">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-6">
                        <span class="bg-primary/10 text-primary px-5 py-2 rounded-full text-sm font-bold inline-flex items-center space-x-2">
                            <FontAwesomeIcon :icon="faQuestionCircle" />
                            <span>FAQ</span>
                        </span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-primary mb-4">
                       {{ pageContent?.sections?.faq?.title || 'Frequently Asked Questions' }}
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        {{ pageContent?.sections?.faq?.description || 'Quick answers to common questions' }}
                    </p>
                </div>

                <!-- FAQ Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                   <div 
    v-for="(faq, index) in pageContent?.sections?.faq?.items || [
        { q: 'How do I start documenting my adventures?', a: 'Simply create an account and start adding your first adventure story with photos and descriptions.', icon: faMapMarkedAlt },
        { q: 'Is there a mobile app?', a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.', icon: faMobileAlt },
        { q: 'Can I collaborate with friends?', a: 'Absolutely! You can create shared adventure logs with multiple contributors.', icon: faUsers },
        { q: 'Is my data secure?', a: 'We use enterprise-grade security to protect your stories and personal information.', icon: faShieldAlt }
    ]" 
    :key="faq.q"
    class="group bg-white p-8 rounded-2xl border-2 border-gray-100 hover:border-primary transition-all duration-300 hover:shadow-xl"
>
    <div class="flex items-start space-x-4">
        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-accent shadow-lg group-hover:scale-110 transition-transform">
            <FontAwesomeIcon :icon="getFAQIcon(faq.icon)" class="text-lg" />
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-primary text-lg mb-3 group-hover:text-secondary transition-colors">
                {{ faq.q }}
            </h3>
            <p class="text-gray-600 leading-relaxed">{{ faq.a }}</p>
        </div>
    </div>
</div>
                </div>

                <!-- Additional Help CTA -->
                <div class="mt-12 text-center">
                    <p class="text-gray-600 mb-4">Still have questions?</p>
                    <button class="inline-flex items-center space-x-2 bg-primary text-accent px-8 py-4 rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg">
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
        transform: translateY(-15px) rotate(3deg);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(-3deg);
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
        transform: translateY(-10px) translateX(-50%);
    }
}
</style>