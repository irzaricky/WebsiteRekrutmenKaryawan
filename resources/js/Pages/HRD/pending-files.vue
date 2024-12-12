<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Link } from "@inertiajs/vue3";

// Props
defineProps({
    title: String,
    candidates: Object,
});

// Add search query state
const searchQuery = ref("");

// Filters state
const filters = ref({
    notUploaded: false,
});

// Clean URL parameters
const cleanParams = (params) => {
    const cleanedParams = {};
    Object.keys(params).forEach((key) => {
        if (
            params[key] !== null &&
            params[key] !== undefined &&
            params[key] !== ""
        ) {
            cleanedParams[key] = params[key];
        }
    });
    return cleanedParams;
};

// Add getPaginationUrl helper
const getPaginationUrl = (url) => {
    if (!url) return null;
    const urlObj = new URL(url);

    // Preserve filter state
    if (filters.value.notUploaded) {
        urlObj.searchParams.set("not_uploaded", "true");
    }
    if (searchQuery.value) {
        urlObj.searchParams.set("search", searchQuery.value);
    }

    return urlObj.toString();
};

// Add getRequiredIjazah helper
const getRequiredIjazah = (level) => {
    switch (level) {
        case "SMA":
            return ["ijazah_smp", "ijazah_sma"];
        case "D3":
            return ["ijazah_smp", "ijazah_sma", "ijazah_d3"];
        case "S1":
            return ["ijazah_smp", "ijazah_sma", "ijazah_s1"];
        case "S2":
            return ["ijazah_smp", "ijazah_sma", "ijazah_s1", "ijazah_s2"];
        case "S3":
            return [
                "ijazah_smp",
                "ijazah_sma",
                "ijazah_s1",
                "ijazah_s2",
                "ijazah_s3",
            ];
        default:
            return [];
    }
};

// Handle filter changes with debounce
const handleFiltersChange = debounce(() => {
    const currentQuery = new URLSearchParams(window.location.search);
    const currentPage = currentQuery.get("page");

    const params = cleanParams({
        search: searchQuery.value,
        not_uploaded: filters.value.notUploaded,
        page: currentPage, // Preserve current page
    });

    router.get(route("dashboard.pending-files"), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

// Watch for changes in both search and filters
watch([() => filters.value.notUploaded, searchQuery], () => {
    handleFiltersChange();
});

// Initialize from URL params on mount
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    filters.value.notUploaded = urlParams.get("not_uploaded") === "true";
    searchQuery.value = urlParams.get("search") || "";
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
            <!-- Search and Filter Section -->
            <div class="mb-4 flex items-center justify-between">
                <!-- Search Input -->
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

                <!-- Filter Checkbox -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="notUploaded"
                        v-model="filters.notUploaded"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                    />
                    <label for="notUploaded" class="ml-2 text-sm text-gray-600">
                        Show only candidates with no files uploaded
                    </label>
                </div>
            </div>

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
                                    'Ijazah Status',
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

                            <!-- Photo Status -->
                            <td :class="TABLE_CELL_CLASSES">
                                <span
                                    :class="[
                                        STATUS_BADGE_BASE,
                                        getStatusBadgeClass(
                                            candidate.candidate_detail
                                                ?.photo_status || 'not uploaded'
                                        ),
                                    ]"
                                >
                                    {{
                                        candidate.candidate_detail
                                            ?.photo_status || "Not uploaded"
                                    }}
                                </span>
                            </td>

                            <!-- CV Status -->
                            <td :class="TABLE_CELL_CLASSES">
                                <span
                                    :class="[
                                        STATUS_BADGE_BASE,
                                        getStatusBadgeClass(
                                            candidate.candidate_detail
                                                ?.cv_status || 'not uploaded'
                                        ),
                                    ]"
                                >
                                    {{
                                        candidate.candidate_detail?.cv_status ||
                                        "Not uploaded"
                                    }}
                                </span>
                            </td>

                            <!-- Ijazah Status -->
                            <td :class="TABLE_CELL_CLASSES">
                                <div class="space-y-1">
                                    <template
                                        v-if="
                                            candidate.candidate_detail
                                                ?.education_level
                                        "
                                    >
                                        <span
                                            v-for="ijazah in getRequiredIjazah(
                                                candidate.candidate_detail
                                                    .education_level
                                            )"
                                            :key="ijazah"
                                            :class="[
                                                STATUS_BADGE_BASE,
                                                getStatusBadgeClass(
                                                    candidate.candidate_detail[
                                                        `${ijazah}_status`
                                                    ] || 'not uploaded'
                                                ),
                                                'block',
                                            ]"
                                        >
                                            {{
                                                ijazah
                                                    .split("_")[1]
                                                    .toUpperCase()
                                            }}:
                                            {{
                                                candidate.candidate_detail[
                                                    `${ijazah}_status`
                                                ] || "Not uploaded"
                                            }}
                                        </span>
                                    </template>
                                    <span v-else class="text-gray-500 text-sm">
                                        No education level set
                                    </span>
                                </div>
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
                                :href="getPaginationUrl(candidates[label])"
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
