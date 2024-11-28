<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const searchQuery = ref("");
const selectedTimeFilter = ref("all");
const selectedActionType = ref("all");

const props = defineProps({
    title: {
        type: String,
    },
    actions: {
        type: Object,
        required: true,
    },
});

const timeFilters = [
    { value: "all", label: "Semua" },
    { value: "hour", label: "1 Jam Terakhir" },
    { value: "day", label: "1 hari Terakhir" },
    { value: "week", label: "1 Minggu Terakhir" },
    { value: "month", label: "1 Bulan Terakhir" },
];

const actionTypes = [
    { value: "all", label: "All Actions" },
    { value: "create", label: "Create Score" },
    { value: "update", label: "Update Score" },
    { value: "update_file_status", label: "Update File Status" },
];

// Debounced search function
const handleSearch = debounce((value) => {
    router.get(
        route("dashboard.history"),
        {
            search: value,
            time_filter: selectedTimeFilter.value,
            action_type: selectedActionType.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, 300);

// Watch for changes
watch(
    [searchQuery, selectedTimeFilter, selectedActionType],
    ([searchValue, timeValue, actionValue]) => {
        router.get(
            route("dashboard.history"),
            {
                search: searchValue,
                time_filter: timeValue,
                action_type: actionValue,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }
);

// Format the action type for display
const formatActionType = (type) => {
    switch (type) {
        case "create":
            return "Create Score";
        case "update":
            return "Update Score";
        case "update_file_status":
            return "Update File Status";
        default:
            return type;
    }
};
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="p-4 pt-3 flex-1">
            <!-- Filter Controls -->
            <div class="flex gap-4 mb-6">
                <!-- Search HRD Name -->
                <div class="flex-1">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama HRD..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Time Filter -->
                <select
                    v-model="selectedTimeFilter"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option
                        v-for="filter in timeFilters"
                        :key="filter.value"
                        :value="filter.value"
                    >
                        {{ filter.label }}
                    </option>
                </select>

                <!-- Action Type Filter -->
                <select
                    v-model="selectedActionType"
                    class="pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option
                        v-for="type in actionTypes"
                        :key="type.value"
                        :value="type.value"
                    >
                        {{ type.label }}
                    </option>
                </select>
            </div>

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            HRD
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Action Type
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Details
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Timestamp
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="action in actions.data" :key="action.id">
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                        >
                            {{ action.hrd.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ formatActionType(action.action_type) }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                        >
                            {{ action.details }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                        >
                            {{ new Date(action.created_at).toLocaleString() }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Sidebar>
</template>
