<script setup>
import { Head } from "@inertiajs/vue3";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { defineProps, ref, computed } from "vue";

const props = defineProps({
    title: String,
    candidates: {
        type: Array,
        required: true,
    },
});

const rankFilter = ref("");
const searchQuery = ref("");

// Computed property for filtered candidates
const filteredCandidates = computed(() => {
    let filtered = [...props.candidates];

    // First apply rank filter if valid
    if (rankFilter.value && !isNaN(rankFilter.value)) {
        const rankLimit = parseInt(rankFilter.value);
        if (rankLimit > 0 && rankLimit <= props.candidates.length) {
            filtered = filtered.slice(0, rankLimit);
        }
    }

    // Then apply search filter
    if (searchQuery.value.trim()) {
        filtered = filtered.filter((candidate) =>
            candidate.name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())
        );
    }

    return filtered;
});

// Computed property for displaying filter info
const filterInfo = computed(() => {
    const total = props.candidates.length;
    const filtered = filteredCandidates.value.length;

    if (filtered === total) return `Showing all ${total} candidates`;
    return `Showing ${filtered} out of ${total} candidates`;
});
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="container mx-auto px-4">
            <!-- Filter Section -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ranking Filter -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Filter Ranking
                        </label>
                        <input
                            type="number"
                            v-model="rankFilter"
                            placeholder="Enter ranking limit..."
                            min="1"
                            :max="candidates.length"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <!-- Search Filter -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Search Candidate
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Search by name..."
                                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            />
                            <span class="absolute left-3 top-2.5 text-gray-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
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
                </div>
            </div>

            <!-- Filter Info moved here -->
            <div class="flex justify-end mb-2">
                <div class="text-sm text-gray-600">
                    {{ filterInfo }}
                </div>
            </div>

            <div class="relative h-[530px] rounded-lg shadow-md">
                <div
                    class="overflow-y-auto h-full scrollbar scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100"
                >
                    <table class="min-w-full bg-white">
                        <thead class="sticky top-0 bg-white shadow-sm">
                            <tr>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    Rank
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    Name
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    TIU Score
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    TWK Score
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    TKB Score
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    TW Score
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    Final Score
                                </th>
                                <th class="py-2 px-4 border-b bg-gray-50">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(candidate, index) in filteredCandidates"
                                :key="candidate.id"
                                :class="{
                                    'bg-green-100 hover:bg-green-200':
                                        candidate.is_accepted,
                                    'bg-red-100 hover:bg-red-200':
                                        !candidate.is_accepted,
                                }"
                            >
                                <td class="py-2 px-4 border-b">
                                    {{ index + 1 }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.name }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.tiu_score || "-" }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.twk_score || "-" }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.tkb_score || "-" }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.tw_score || "-" }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ candidate.final_score.toFixed(2) }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <span
                                        :class="{
                                            'px-2 py-1 rounded text-sm font-medium': true,
                                            'bg-green-500 text-white':
                                                candidate.is_accepted,
                                            'bg-red-500 text-white':
                                                !candidate.is_accepted,
                                        }"
                                    >
                                        {{
                                            candidate.is_accepted
                                                ? "Diterima"
                                                : "Tidak Diterima"
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
