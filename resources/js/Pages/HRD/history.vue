<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const searchQuery = ref("");
const selectedTimeFilter = ref("all");
const selectedActionType = ref("all");

const props = defineProps({
    title: String,
    actions: Object, // Paginated data
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
            page: 1, // Reset to first page on search
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, 300);

// Modified handleFiltersChange with proper state preservation
const handleFiltersChange = debounce(() => {
    const currentQuery = new URLSearchParams(window.location.search);
    const currentPage = currentQuery.get("page");

    const params = {
        search: searchQuery.value || undefined,
        time_filter:
            selectedTimeFilter.value !== "all"
                ? selectedTimeFilter.value
                : undefined,
        action_type:
            selectedActionType.value !== "all"
                ? selectedActionType.value
                : undefined,
        page: currentPage, // Preserve current page
    };

    // Clean undefined values
    Object.keys(params).forEach(
        (key) => params[key] === undefined && delete params[key]
    );

    router.get(route("dashboard.history"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

watch(
    [searchQuery, selectedTimeFilter, selectedActionType],
    () => {
        handleFiltersChange();
    },
    { deep: true }
);

// Format action type for display
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

// Style constants
const PAGINATION_BUTTON_BASE =
    "px-4 py-2 text-sm rounded-md transition-colors duration-150";

// Modified getPaginationUrl to preserve active filters
const getPaginationUrl = (url) => {
    if (!url) return null;
    const urlObj = new URL(url);

    // Only add params if they're not default values
    if (selectedTimeFilter.value !== "all") {
        urlObj.searchParams.set("time_filter", selectedTimeFilter.value);
    }
    if (selectedActionType.value !== "all") {
        urlObj.searchParams.set("action_type", selectedActionType.value);
    }
    if (searchQuery.value) {
        urlObj.searchParams.set("search", searchQuery.value);
    }

    return urlObj.toString();
};

// Initialize from URL params on mount
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    searchQuery.value = urlParams.get("search") || "";
    selectedTimeFilter.value = urlParams.get("time_filter") || "all";
    selectedActionType.value = urlParams.get("action_type") || "all";
});
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

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Table Section -->
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
                                {{
                                    new Date(action.created_at).toLocaleString()
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination Section -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <!-- Results Counter -->
                        <div class="text-sm text-gray-500">
                            Showing {{ actions.from }} to {{ actions.to }} of
                            {{ actions.total }} results
                        </div>

                        <!-- Pagination Controls -->
                        <div class="flex gap-2">
                            <Link
                                v-for="(url, label) in {
                                    prev_page_url: 'Previous',
                                    next_page_url: 'Next',
                                }"
                                :key="label"
                                :href="getPaginationUrl(actions[label])"
                                :class="[
                                    PAGINATION_BUTTON_BASE,
                                    actions[label]
                                        ? 'bg-blue-500 text-white hover:bg-blue-600'
                                        : 'bg-gray-200 text-gray-500 cursor-not-allowed',
                                ]"
                                :disabled="!actions[label]"
                            >
                                {{
                                    label === "prev_page_url"
                                        ? "Previous"
                                        : "Next"
                                }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
