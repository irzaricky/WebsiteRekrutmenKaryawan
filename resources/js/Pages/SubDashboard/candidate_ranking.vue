<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { defineProps, ref, computed } from "vue";

const props = defineProps({
    candidates: {
        type: Array,
        required: true,
    },
});

const rankFilter = ref("");

const filteredCandidates = computed(() => {
    if (!rankFilter.value || isNaN(rankFilter.value)) {
        return props.candidates;
    }
    return props.candidates.slice(0, parseInt(rankFilter.value));
});
</script>

<template>
    <Sidebar>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">Candidate Rankings</h1>

            <!-- Filter Input -->
            <div class="mb-4 flex justify-between">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Ranking
                    </label>
                    <input
                        type="number"
                        v-model="rankFilter"
                        placeholder="Masukkan batas ranking..."
                        min="1"
                        :max="candidates.length"
                        class="w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
                <!-- Menampilkan informasi total kandidat -->
                <div class="pt-10 mt-4 text-sm text-gray-600">
                    Showing {{ filteredCandidates.length }} out of
                    {{ candidates.length }} candidates
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
