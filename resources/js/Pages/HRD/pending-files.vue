<script setup>
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
        }[status] || "bg-gray-100 text-gray-800"
    );
};
</script>

<template>
    <Sidebar>
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Pending Files Review</h2>

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
                                                ?.photo_status
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
                                                ?.cv_status
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
                                                ?.certificate_status
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
            </div>
        </div>
    </Sidebar>
</template>
