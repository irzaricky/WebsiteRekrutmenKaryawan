<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    title: {
        type: String,
    },
    actions: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div class="p-6 pt-3 flex-1">
            <h1 class="text-2xl font-semibold">History</h1>
            <table class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            HRD
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Action Type
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Details
                        </th>
                        <th
                            scope="col"
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
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                        >
                            {{ action.action_type }}
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
            <div class="mt-4">
                <Link
                    v-if="actions.prev_page_url"
                    :href="actions.prev_page_url"
                    class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                >
                    Previous
                </Link>
                <Link
                    v-if="actions.next_page_url"
                    :href="actions.next_page_url"
                    class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                >
                    Next
                </Link>
            </div>
        </div>
    </Sidebar>
</template>
