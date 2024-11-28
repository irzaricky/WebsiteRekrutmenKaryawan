<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const img = [{ id: "1", href: "../assets/images/for.png", link: "/" }];
const searchQuery = ref("");

const props = defineProps({
    title: {
        type: String,
    },
    candidates: Object,
});

// Debounced search function
const handleSearch = debounce((value) => {
    router.get(
        route("dashboard.data-candidate"),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, 300);

// Watch for changes in search input
watch(searchQuery, (value) => {
    handleSearch(value);
});
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="space-y-4">
            <!-- Header with Search -->
            <div class="flex justify-between items-center">
                <!-- Search Input -->
                <div class="relative w-64">
                    <!-- Reduced width with w-64 -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search candidate name..."
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span class="absolute right-3 top-1.5 text-gray-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </span>
                </div>

                <!-- Pagination Navigation -->
                <nav aria-label="Page navigation">
                    <ul class="flex space-x-2">
                        <li v-if="candidates.prev_page_url">
                            <Link
                                :href="candidates.prev_page_url"
                                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                            >
                                Previous
                            </Link>
                        </li>
                        <li v-if="candidates.next_page_url">
                            <Link
                                :href="candidates.next_page_url"
                                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                            >
                                Next
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div>
            <ul role="list" class="divide-y divide-gray-100">
                <li
                    v-for="candidate in candidates.data"
                    :key="candidate.id"
                    class="flex justify-between gap-x-4 py-2"
                >
                    <div class="flex min-w-0 gap-x-4">
                        <img
                            class="h-12 w-12 flex-none rounded-full bg-gray-50"
                            :src="img[0].href"
                            alt=""
                        />
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold text-gray-900">
                                {{ candidate.name }}
                            </p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">
                                {{ candidate.email }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="hidden shrink-0 sm:flex sm:items-center space-x-2"
                    >
                        <button
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-2 rounded inline-flex items-center"
                        >
                            <Link
                                :href="`/edit-data-candidate/${candidate.id}`"
                                class="edit-button"
                            >
                                Edit Nilai
                            </Link>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </Sidebar>
</template>
