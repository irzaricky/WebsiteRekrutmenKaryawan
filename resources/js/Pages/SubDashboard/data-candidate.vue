<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";
const img = [{ id: "1", href: "../assets/images/for.png", link: "/" }];

defineProps({
    title: {
        type: String,
    },
    candidates: Object,
});
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-light">Data Candidate</h2>
            <nav aria-label="Page navigation">
                <ul class="flex space-x-2">
                    <li v-if="candidates.prev_page_url">
                        <Link
                            :href="candidates.prev_page_url"
                            class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                        >
                            Previous
                        </Link>
                    </li>
                    <li v-if="candidates.next_page_url">
                        <Link
                            :href="candidates.next_page_url"
                            class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-200 transition"
                        >
                            Next
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>

        <div>
            <ul role="list" class="divide-y divide-gray-100">
                <li
                    v-for="candidate in candidates.data"
                    :key="candidate.id"
                    class="flex justify-between gap-x-4 py-2"
                >
                    <div class="flex min-w-0 gap-x-4">
                        <img
                            class="h-12 w-12 flex-none rounded-full bg-gray-50"
                            :src="img[0].href"
                            alt=""
                        />
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold text-gray-900">
                                {{ candidate.name }}
                            </p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">
                                {{ candidate.email }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="hidden shrink-0 sm:flex sm:flex-col sm:items-end"
                    >
                        <button
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-2 rounded inline-flex items-center"
                        >
                            <Link
                                :href="`/dashboard/edit-data-candidate/${candidate.id}`"
                                class="edit-button"
                            >
                                Edit Nilai
                            </Link>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </Sidebar>
</template>
