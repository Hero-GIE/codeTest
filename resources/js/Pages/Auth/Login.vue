<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useForm, Head, Link, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();

// Get CSRF token from page props
const csrfToken = computed(() => page.props.csrf_token);

const form = useForm({
    email: '',
    password: '',
   
});

// Safely access flash messages
const flashError = computed(() => page.props.flash?.error || '');
const flashSuccess = computed(() => page.props.flash?.success || '');

// Watch for form errors and log them
watch(() => form.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        console.log('Form errors detected:', errors);
    }
}, { deep: true });

// Update token when component mounts
onMounted(() => {
    form._token = csrfToken.value;
    console.log('Login component mounted, CSRF token:', !!csrfToken.value);
});

const submit = () => {
    console.log('Submitting login form...', {
        email: form.email,
        hasToken: !!csrfToken.value,
        token: csrfToken.value
    });
    
    // ✅ The token will be automatically included by Inertia
    form.post('/login', {
        preserveScroll: true,
        onFinish: () => {
            form.reset('password');
            console.log('Login request finished');
        },
        onError: (errors) => {
            console.log('Login form errors:', errors);
        },
        onSuccess: () => {
            console.log('Login successful - should redirect to dashboard');
        },
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center bg-white px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 sm:w-48 sm:h-48 lg:w-64 lg:h-64 bg-gradient-to-r from-black to-gray-800 rounded-full blur-2xl sm:blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 right-1/4 w-48 h-48 sm:w-64 sm:h-64 lg:w-96 lg:h-96 bg-gradient-to-r from-gray-800 to-gray-900 rounded-full blur-2xl sm:blur-3xl"></div>
            <div class="absolute top-1/2 right-1/3 w-48 h-48 sm:w-64 sm:h-64 lg:w-96 lg:h-96 bg-gradient-to-r from-gray-900 to-black rounded-full blur-xl sm:blur-2xl animate-bounce"></div>
        </div>

        <div class="w-full max-w-xs sm:max-w-sm md:max-w-md relative z-10">
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg sm:shadow-2xl shadow-black/40 sm:shadow-black/50 border border-gray-300 p-6 sm:p-8 transition-all duration-500 hover:shadow-xl sm:hover:shadow-3xl hover:shadow-black/20 hover:border-gray-400">
                
                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="flex justify-center mb-3 sm:mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-black to-gray-800 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg shadow-black/30 animate-float">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-black to-gray-800 bg-clip-text text-transparent mb-2">
                        Adventure Awaits
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 font-light">
                        Sign in to continue your journey
                    </p>
                </div>

                <!-- ✅ IMPROVED: Show session errors with better visibility -->
                <div v-if="flashError" class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 border border-red-300 rounded-lg sm:rounded-xl text-red-800 text-xs sm:text-sm animate-pulse">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ flashError }}</span>
                    </div>
                </div>

                <!-- Show session success -->
                <div v-if="flashSuccess" class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-300 rounded-lg sm:rounded-xl text-green-800 text-xs sm:text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ flashSuccess }}</span>
                    </div>
                </div>

                <!-- Show form validation errors -->
                <div v-if="form.hasErrors" class="mb-4 sm:mb-6 p-3 sm:p-4 bg-yellow-50 border border-yellow-300 rounded-lg sm:rounded-xl text-yellow-800 text-xs sm:text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium">Please check your credentials and try again.</span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
                  

                    <!-- Email Field -->
                    <div class="group">
                        <InputLabel for="email" value="Email" class="mb-2 sm:mb-3 text-xs sm:text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-white border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-md sm:group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500 text-sm sm:text-base"
                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email || flashError }"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Enter your email"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-600" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="group">
                        <InputLabel for="password" value="Password" class="mb-2 sm:mb-3 text-xs sm:text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-white border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-md sm:group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500 text-sm sm:text-base"
                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password || flashError }"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-600" :message="form.errors.password" />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 sm:pt-4">
                        <PrimaryButton
                            class="w-full flex items-center justify-center gap-2 sm:gap-3 py-3 sm:py-4 px-4 sm:px-6 bg-gradient-to-r from-black to-gray-800 hover:from-gray-800 hover:to-gray-900 focus:ring-2 focus:ring-black focus:ring-offset-2 focus:ring-offset-white text-white font-semibold rounded-lg sm:rounded-xl shadow-lg shadow-black/25 hover:shadow-xl hover:shadow-black/30 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] border border-gray-800/20 text-sm sm:text-base"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing, 'hover:scale-100': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            {{ form.processing ? 'Signing in...' : 'Continue Journey' }}
                        </PrimaryButton>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center pt-4 sm:pt-6 border-t border-gray-200">
                        <p class="text-xs sm:text-sm text-gray-600">
                            New adventurer?
                            <Link
                                :href="route('register')"
                                class="font-semibold text-black hover:text-gray-800 transition-all duration-200 hover:underline ml-1 group"
                            >
                                Begin your journey
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </Link>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer Note -->
            <div class="text-center mt-4 sm:mt-6">
                <p class="text-xs text-gray-500">
                    Your adventure log awaits. Secure and private.
                </p>
            </div>
        </div>
    </div>
</template>
<style scoped>
/* Custom animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Enhanced mobile responsiveness */
@media (max-width: 640px) {
    .animate-float {
        animation-duration: 4s;
    }
}

/* Custom scrollbar for dark mode */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Enhanced focus styles for mobile */
input:focus {
    outline: none;
    ring: 2px;
}

/* Prevent zoom on iOS for inputs */
@media (max-width: 768px) {
    input, select, textarea {
        font-size: 16px !important;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .hover\:scale-\[1\.02\]:hover {
        transform: scale(1);
    }
    
    .group-hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }
}
</style>