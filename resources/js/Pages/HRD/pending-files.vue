<script setup>
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    title: String,
    candidates: Object,
});

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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Photo Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                CV Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Certificate Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="candidate in candidates.data"
                            :key="candidate.id"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ candidate.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs',
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs',
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs',
                                        getStatusBadgeClass(
                                            candidate.candidate_detail
                                                ?.certificate_status ||
                                                'not uploaded'
                                        ),
                                    ]"
                                >
                                    {{
                                        candidate.candidate_detail
                                            ?.certificate_status ||
                                        "Not uploaded"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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

                <!-- Modify the pagination controls section -->
                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing {{ candidates.from }} to
                            {{ candidates.to }} of
                            {{ candidates.total }} results
                        </div>
                        <div class="flex gap-2">
                            <!-- Previous button -->
                            <Link
                                :href="candidates.prev_page_url"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md',
                                    candidates.prev_page_url
                                        ? 'bg-blue-500 text-white hover:bg-blue-600'
                                        : 'bg-gray-200 text-gray-500 cursor-not-allowed',
                                ]"
                                :disabled="!candidates.prev_page_url"
                            >
                                Previous
                            </Link>

                            <!-- Next button -->
                            <Link
                                :href="candidates.next_page_url"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md',
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
        </div>
    </Sidebar>
</template>
