<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const img = [{ id: "1", href: "../assets/images/for.png", link: "/" }];
const searchQuery = ref("");

const props = defineProps({
    title: String,
    candidates: Object,
});

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

watch(searchQuery, (value) => {
    handleSearch(value);
});
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <!-- Search Header -->
            <div class="mb-6">
                <div class="relative w-64">
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
            </div>

            <!-- Candidate List -->
            <ul role="list" class="divide-y divide-gray-100 mb-6">
                <li
                    v-for="candidate in candidates.data"
                    :key="candidate.id"
                    class="flex justify-between items-center py-4"
                >
                    <div class="flex items-center gap-4">
                        <img
                            :src="img[0].href"
                            alt=""
                            class="h-12 w-12 rounded-full bg-gray-50"
                        />
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ candidate.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ candidate.email }}
                            </p>
                        </div>
                    </div>
                    <Link
                        :href="`/edit-data-candidate/${candidate.id}`"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1.5 px-3 rounded text-sm"
                    >
                        Edit Nilai
                    </Link>
                </li>
            </ul>

            <!-- Pagination Footer -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ candidates.from }} to {{ candidates.to }} of
                        {{ candidates.total }} results
                    </p>
                    <div class="flex gap-2">
                        <Link
                            :href="candidates.prev_page_url"
                            :class="[
                                'px-4 py-2 text-sm rounded-md transition-colors duration-150',
                                candidates.prev_page_url
                                    ? 'bg-blue-500 text-white hover:bg-blue-600'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed',
                            ]"
                            :disabled="!candidates.prev_page_url"
                        >
                            Previous
                        </Link>
                        <Link
                            :href="candidates.next_page_url"
                            :class="[
                                'px-4 py-2 text-sm rounded-md transition-colors duration-150',
                                candidates.next_page_url
                                    ? 'bg-blue-500 text-white hover:bg-blue-600'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed',
                            ]"
                            :disabled="!candidates.next_page_url"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
