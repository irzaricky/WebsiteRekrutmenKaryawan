<script setup>
import Sidebar from "@/components/Dashboard/Sidebar.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement,
} from "chart.js";
import { Pie, Bar } from "vue-chartjs";

ChartJS.register(
    ArcElement,
    CategoryScale,
    LinearScale,
    BarElement,
    Tooltip,
    Legend
);

const props = defineProps({
    title: String,
    averageScores: Object,
    statistics: Object,
});

// Setup chart data
const testScoresData = {
    labels: ["TIU", "TWK", "TKB", "TW"],
    datasets: [
        {
            label: "Rata-rata Skor",
            data: Object.values(props.averageScores),
            backgroundColor: [
                "rgba(255, 99, 132, 0.5)",
                "rgba(54, 162, 235, 0.5)",
                "rgba(255, 206, 86, 0.5)",
                "rgba(75, 192, 192, 0.5)",
            ],
            borderColor: [
                "rgba(255, 99, 132, 1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)",
            ],
            borderWidth: 1,
        },
    ],
};
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Dashboard Analytics</h1>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">
                        Total Kandidat
                    </h3>
                    <p class="text-3xl font-bold">
                        {{ statistics.total_candidates }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">
                        Kandidat Diterima
                    </h3>
                    <p class="text-3xl font-bold text-green-600">
                        {{ statistics.accepted_candidates }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">
                        Tingkat Kelulusan
                    </h3>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ statistics.acceptance_rate }}%
                    </p>
                </div>
            </div>

            <!-- Charts and Accepted Candidates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Average Test Scores Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        Rata-rata Skor Test
                    </h3>
                    <Bar
                        :data="testScoresData"
                        :options="{ responsive: true }"
                    />
                </div>

                <!-- Accepted Candidates Table -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        Kandidat Diterima
                    </h3>
                    <div class="max-h-[400px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white sticky top-0">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase bg-white"
                                    >
                                        Rank
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase bg-white"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase bg-white"
                                    >
                                        Final Score
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(
                                        candidate, index
                                    ) in statistics.accepted_list"
                                    :key="candidate.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        {{ index + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        {{ candidate.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        {{ candidate.final_score.toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
