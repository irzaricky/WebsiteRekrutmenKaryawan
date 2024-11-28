<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
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

// Add test name constants
const TEST_NAMES = ["TIU", "TWK", "TKB", "TW"];

const TABLE_HEADER_CLASSES =
    "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";
const TABLE_CELL_CLASSES = "px-6 py-4 whitespace-nowrap text-sm text-gray-900";

// Get score for specific test
const getTestScore = (candidate: any, testName: string) => {
    return (
        candidate.test_results?.find((result) => result.test.name === testName)
            ?.score || "-"
    );
};
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

            <!-- Candidates Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" :class="TABLE_HEADER_CLASSES">
                                Photo
                            </th>
                            <th scope="col" :class="TABLE_HEADER_CLASSES">
                                Name
                            </th>
                            <th
                                v-for="test in TEST_NAMES"
                                :key="test"
                                scope="col"
                                :class="TABLE_HEADER_CLASSES"
                            >
                                {{ test }}
                            </th>
                            <th scope="col" :class="TABLE_HEADER_CLASSES">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="candidate in candidates.data"
                            :key="candidate.id"
                        >
                            <td :class="TABLE_CELL_CLASSES">
                                <img
                                    :src="img[0].href"
                                    :alt="candidate.name"
                                    class="h-10 w-10 rounded-full"
                                />
                            </td>
                            <td :class="TABLE_CELL_CLASSES">
                                <div>
                                    <div class="font-medium">
                                        {{ candidate.name }}
                                    </div>
                                    <div class="text-gray-500 text-xs">
                                        {{ candidate.email }}
                                    </div>
                                </div>
                            </td>
                            <td
                                v-for="test in TEST_NAMES"
                                :key="test"
                                :class="TABLE_CELL_CLASSES"
                            >
                                <span
                                    :class="{
                                        'font-medium': true,
                                        'text-green-600':
                                            getTestScore(candidate, test) >= 80,
                                        'text-yellow-600':
                                            getTestScore(candidate, test) >=
                                                60 &&
                                            getTestScore(candidate, test) < 80,
                                        'text-red-600':
                                            getTestScore(candidate, test) <
                                                60 &&
                                            getTestScore(candidate, test) !==
                                                '-',
                                    }"
                                >
                                    {{ getTestScore(candidate, test) }}
                                </span>
                            </td>
                            <td :class="TABLE_CELL_CLASSES">
                                <Link
                                    :href="
                                        route(
                                            'dashboard.edit-data-candidate',
                                            candidate.id
                                        )
                                    "
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    Edit Nilai
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ candidates.from }} to {{ candidates.to }} of
                        {{ candidates.total }} results
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-for="(url, label) in {
                                prev_page_url: 'Previous',
                                next_page_url: 'Next',
                            }"
                            :key="label"
                            :href="candidates[label]"
                            :class="[
                                'px-4 py-2 text-sm rounded-md transition-colors duration-150',
                                candidates[label]
                                    ? 'bg-blue-500 text-white hover:bg-blue-600'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed',
                            ]"
                            :disabled="!candidates[label]"
                        >
                            {{ url }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
