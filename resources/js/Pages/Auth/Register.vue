<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm, Head, Link, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const nameInput = ref(null);

// Safely access flash messages
const flashError = computed(() => page.props.flash?.error || '');
const flashSuccess = computed(() => page.props.flash?.success || '');

onMounted(() => {
    setTimeout(() => {
        nameInput.value?.focus();
    }, 100);
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <!-- Main Container -->
    <div class="min-h-screen flex items-center justify-center bg-white p-4 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-r from-black to-gray-800 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-gradient-to-r from-gray-800 to-gray-900 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-1/3 w-96 h-96 bg-gradient-to-r from-gray-900 to-black rounded-full blur-2xl animate-bounce"></div>
        </div>

        <!-- Card Container -->
        <div class="w-full max-w-md relative z-10">
            <!-- Register Card -->
            <div class="bg-white rounded-3xl shadow-2xl shadow-black/10 border border-gray-300 p-8 transition-all duration-500 hover:shadow-3xl hover:shadow-black/20 hover:border-gray-400">
                
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-black to-gray-800 rounded-2xl flex items-center justify-center shadow-lg shadow-black/30 animate-float">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-black to-gray-800 bg-clip-text text-transparent mb-2">
                        Begin Your Journey
                    </h1>
                    <p class="text-gray-600 font-light">
                        Create your adventure log account
                    </p>
                </div>

                <!-- Show session errors -->
                <div v-if="flashError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ flashError }}</span>
                    </div>
                </div>

                <!-- Show session success -->
                <div v-if="flashSuccess" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ flashSuccess }}</span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name Field -->
                    <div class="group">
                        <InputLabel for="name" value="Full Name" class="mb-3 text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                ref="nameInput"
                                id="name"
                                type="text"
                                class="mt-1 block w-full pl-12 pr-4 py-4 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500"
                                v-model="form.name"
                                required
                                autocomplete="name"
                                placeholder="Enter your full name"
                            />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-5 w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-2 text-red-600" :message="form.errors.name" />
                    </div>

                    <!-- Email Field -->
                    <div class="group">
                        <InputLabel for="email" value="Email" class="mb-3 text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full pl-12 pr-4 py-4 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Enter your email"
                            />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-5 w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-2 text-red-600" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="group">
                        <InputLabel for="password" value="Password" class="mb-3 text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full pl-12 pr-4 py-4 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Create a password"
                            />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-5 w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-2 text-red-600" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="group">
                        <InputLabel for="password_confirmation" value="Confirm Password" class="mb-3 text-sm font-medium text-gray-700" />
                        <div class="relative">
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full pl-12 pr-4 py-4 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all duration-300 group-hover:shadow-lg group-hover:shadow-black/5 group-hover:border-gray-400 text-gray-900 placeholder-gray-500"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm your password"
                            />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none mt-1">
                                <svg class="h-5 w-5 text-gray-500 group-focus-within:text-black transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                        <InputError class="mt-2 text-red-600" :message="form.errors.password_confirmation" />
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <h4 class="text-sm font-medium text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Password Requirements
                        </h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li class="flex items-center gap-2">
                                <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                At least 6 characters
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Include letters and numbers
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <PrimaryButton
                            class="w-full flex items-center justify-center gap-3 py-4 px-6 bg-gradient-to-r from-black to-gray-800 hover:from-gray-800 hover:to-gray-900 focus:ring-2 focus:ring-black focus:ring-offset-2 focus:ring-offset-white text-white font-semibold rounded-xl shadow-lg shadow-black/25 hover:shadow-xl hover:shadow-black/30 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] border border-gray-800/20"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing, 'hover:scale-100': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            {{ form.processing ? 'Creating Account...' : 'Start Your Adventure' }}
                        </PrimaryButton>
                    </div>

                    <!-- Sign In Link -->
                    <div class="text-center pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <Link
                                :href="route('login')"
                                class="font-semibold text-black hover:text-gray-800 transition-all duration-200 hover:underline ml-1 group"
                            >
                                Sign in to continue
                                <svg class="w-4 h-4 inline-block ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </Link>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer Note -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    Your adventure begins here. Secure and private.
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
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Enhanced focus styles */
input:focus {
    outline: none;
    ring: 2px;
}
</style>