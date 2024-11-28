<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
});
</script>

<template>
    <header class="bg-gray-300 h-[70px] shadow-sm relative">
        <div class="h-full px-6 flex items-center justify-end">
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
