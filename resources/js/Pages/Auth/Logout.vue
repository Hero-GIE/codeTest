<template>
    <!-- Logout Dialog -->
    <div v-if="showDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl p-8 mx-4 max-w-md w-full transform transition-all duration-300 scale-100 ">
            <!-- Dialog Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <FontAwesomeIcon :icon="faSignOutAlt" class="text-white text-2xl" />
                </div>
                <h3 class="text-2xl font-bold text-black mb-2">Log Out</h3>
                <p class="text-black">Are you sure you want to log out?</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
       <button
    @click="closeDialog"
    class="flex-1  text-black hover:bg-gray-600 hover:text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-2 border border-black"
>
    <span>Cancel</span>
</button>


                <button
                    @click="handleLogout"
                    :disabled="loading"
                    class="flex-1 bg-red-600 hover:bg-red-500 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <FontAwesomeIcon :icon="loading ? faSpinner : faSignOutAlt" class="text-sm" :class="{ 'animate-spin': loading }" />
                    <span>{{ loading ? 'Logging Out...' : 'Log Out' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSignOutAlt, faSpinner } from '@fortawesome/free-solid-svg-icons';

// Props
const props = defineProps({
    showDialog: {
        type: Boolean,
        required: true
    }
});

// Emits
const emit = defineEmits(['close']);

// Reactive state
const loading = ref(false);

// Methods
const closeDialog = () => {
    if (!loading.value) {
        emit('close');
    }
};

const handleLogout = async () => {
    if (loading.value) return;
    
    loading.value = true;
    
    try {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }

        const response = await fetch(route('logout'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            // If successful, reload the page to redirect to login
            window.location.reload();
        } else {
            throw new Error(`Logout failed: ${response.status}`);
        }
    } catch (error) {
        console.error('Logout failed:', error);
        loading.value = false;
        // Optionally show error message to user
        alert('Logout failed. Please try again.');
    }
};


</script>