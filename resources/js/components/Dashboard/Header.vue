<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    user: Object,
    title: String,
});

const showDropdown = ref(false);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

// Close dropdown when clicking outside
const closeDropdown = (e) => {
    if (!e.target.closest(".profile-menu")) {
        showDropdown.value = false;
    }
};

// Add event listener for clicks outside dropdown
if (typeof window !== "undefined") {
    document.addEventListener("click", closeDropdown);
}

// Add computed property for profile image
const profileImage = computed(() => {
    return props.user?.hrd_detail?.profile_image_url || null;
});
</script>

<template>
    <header class="bg-[#8D99AE] h-[70px] shadow-sm relative">
        <div class="h-full px-6 flex items-center justify-between">
            <!-- Left side - Title -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">
                    {{ title }}
                </h1>
            </div>

            <!-- Right side - Profile -->
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">
                        {{ user?.name }}
                    </p>
                </div>
                <div class="relative profile-menu">
                    <button
                        type="button"
                        @click="toggleDropdown"
                        class="flex items-center gap-2 text-sm focus:outline-none"
                    >
                        <div
                            class="h-9 w-9 rounded-full bg-blue-500 flex items-center justify-center hover:bg-blue-600 transition-colors duration-200 overflow-hidden"
                        >
                            <img
                                v-if="profileImage"
                                :src="profileImage"
                                class="h-full w-full object-cover"
                                alt="Profile"
                            />
                            <span v-else class="text-white font-medium">
                                {{ user?.name?.[0]?.toUpperCase() }}
                            </span>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        v-if="showDropdown"
                        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                        <Link
                            :href="route('hrd.profile')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            @click="showDropdown = false"
                        >
                            Profile
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
