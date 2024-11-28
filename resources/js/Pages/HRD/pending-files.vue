<script setup>
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Link } from "@inertiajs/vue3";

// Props
defineProps({
    title: String,
    candidates: Object,
});

// Style Constants
const TABLE_HEADER_CLASSES =
    "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";
const TABLE_CELL_CLASSES = "px-6 py-4 whitespace-nowrap";
const STATUS_BADGE_BASE = "px-2 py-1 rounded-full text-xs";
const PAGINATION_BUTTON_BASE =
    "px-4 py-2 text-sm rounded-md transition-colors duration-150";

// Status Badge Classes
const getStatusBadgeClass = (status) => {
    return (
        {
            pending: "bg-yellow-100 text-yellow-800",
            accepted: "bg-green-100 text-green-800",
            rejected: "bg-red-100 text-red-800",
            "not uploaded": "bg-gray-100 text-gray-500",
        }[status] || "bg-gray-100 text-gray-500"
    );
};
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="p-4">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Table Section -->
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- Table Header -->
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                v-for="header in [
                                    'Name',
                                    'Photo Status',
                                    'CV Status',
                                    'Certificate Status',
                                    'Action',
                                ]"
                                :key="header"
                                :class="TABLE_HEADER_CLASSES"
                            >
                                {{ header }}
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="candidate in candidates.data"
                            :key="candidate.id"
                        >
                            <!-- Name -->
                            <td :class="TABLE_CELL_CLASSES">
                                {{ candidate.name }}
                            </td>

                            <!-- Status Cells -->
                            <td
                                v-for="type in ['photo', 'cv', 'certificate']"
                                :key="type"
                                :class="TABLE_CELL_CLASSES"
                            >
                                <span
                                    :class="[
                                        STATUS_BADGE_BASE,
                                        getStatusBadgeClass(
                                            candidate.candidate_detail?.[
                                                `${type}_status`
                                            ] || 'not uploaded'
                                        ),
                                    ]"
                                >
                                    {{
                                        candidate.candidate_detail?.[
                                            `${type}_status`
                                        ] || "Not uploaded"
                                    }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td :class="TABLE_CELL_CLASSES">
                                <Link
                                    :href="
                                        route(
                                            'dashboard.candidate-details',
                                            candidate.id
                                        )
                                    "
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Review Files
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination Section -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <!-- Results Counter -->
                        <div class="text-sm text-gray-500">
                            Showing {{ candidates.from }} to
                            {{ candidates.to }} of
                            {{ candidates.total }} results
                        </div>

                        <!-- Pagination Controls -->
                        <div class="flex gap-2">
                            <Link
                                v-for="(url, label) in {
                                    prev_page_url: 'Previous',
                                    next_page_url: 'Next',
                                }"
                                :key="label"
                                :href="candidates[label]"
                                :class="[
                                    PAGINATION_BUTTON_BASE,
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
        </div>
    </Sidebar>
</template>
