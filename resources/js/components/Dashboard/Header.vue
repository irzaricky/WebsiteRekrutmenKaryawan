<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
    title: String, // Add title prop
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
                <div class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-2 text-sm focus:outline-none"
                    >
                        <!-- Profile circle with first letter of name -->
                        <div
                            class="h-9 w-9 rounded-full bg-blue-500 flex items-center justify-center"
                        >
                            <span class="text-white font-medium">
                                {{ user?.name?.[0]?.toUpperCase() }}
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>
