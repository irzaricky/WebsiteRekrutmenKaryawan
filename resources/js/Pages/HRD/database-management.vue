<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import Sidebar from "@/components/Dashboard/Sidebar.vue";
import { onMounted, ref, onUnmounted, watch } from "vue";

const props = defineProps({
    title: String,
    backups: {
        type: Array,
        default: () => [],
    },
    flash: Object,
});

const backupsList = ref(props.backups);
let refreshInterval;

// Format file size helper
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

// Confirm restore
const confirmRestore = (filename) => {
    if (
        confirm(
            "Are you sure you want to restore this backup? This will overwrite current database."
        )
    ) {
        router.post(route("backup.restore"), { backup_file: filename });
    }
};

const confirmDelete = (filename) => {
    if (
        confirm(
            "Are you sure you want to delete this backup? This action cannot be undone."
        )
    ) {
        router.delete(route("backup.delete", filename));
    }
};

// Refresh function to fetch latest backups
const refreshBackups = () => {
    router.reload({ only: ["backups"] });
};

// Watch for props.backups changes
watch(
    () => props.backups,
    (newBackups) => {
        backupsList.value = newBackups;
    }
);

onMounted(() => {
    refreshInterval = setInterval(refreshBackups, 60000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// Add dropdown state
const activeDropdown = ref(null);

// Toggle dropdown
const toggleDropdown = (id) => {
  activeDropdown.value = activeDropdown.value === id ? null : id;
};

// Click outside handler
const handleClickOutside = (event) => {
  if (!event.target.closest('.dropdown-menu')) {
    activeDropdown.value = null;
  }
};

// Add click outside listener
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <div class="p-4">
            <!-- Flash Messages -->
            <div
                v-if="$page.props.flash.success"
                class="mb-4 p-4 bg-green-100 text-green-700 rounded"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                v-if="$page.props.flash.error"
                class="mb-4 p-4 bg-red-100 text-red-700 rounded"
            >
                {{ $page.props.flash.error }}
            </div>

            <!-- Actions -->
            <div class="mb-6 flex items-center gap-4">
                <Link
                    :href="route('backup.create')"
                    method="post"
                    as="button"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                        />
                    </svg>
                    Create Database Backup
                </Link>

                <Link
                    :href="route('backup.files')"
                    method="post"
                    as="button"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                        />
                    </svg>
                    Backup Project Files
                </Link>
            </div>

            <!-- Backups Table -->
            <div class="bg-white rounded-lg shadow">
                <!-- Responsive container with horizontal scroll -->
                <div class="relative w-full overflow-x-auto">
                    <!-- Fixed height container with vertical scroll -->
                    <div class="max-h-[500px] overflow-y-auto">
                        <!-- Force table to full width -->
                        <table
                            class="w-full table-fixed divide-y divide-gray-200"
                        >
                            <thead>
                                <tr class="bg-gray-50 sticky top-0 z-10">
                                    <!-- Specify fixed column widths -->
                                    <th
                                        scope="col"
                                        class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Filename
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Category
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Type
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Size
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Created By
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Created At
                                    </th>
                                    <th
                                        scope="col"
                                        class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <!-- In tbody, add text truncate to cells -->
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="backup in backupsList"
                                    :key="backup.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 truncate">
                                        <div
                                            class="truncate"
                                            :title="backup.filename"
                                        >
                                            {{ backup.filename }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs rounded-full',
                                                backup.category === 'automatic'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : 'bg-green-100 text-green-800',
                                            ]"
                                        >
                                            {{ backup.category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs rounded-full',
                                                backup.type === 'database'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : 'bg-green-100 text-green-800',
                                            ]"
                                        >
                                            {{ backup.type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ formatFileSize(backup.size) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ backup.created_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ backup.created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative">
                                            <button
                                                @click.stop="toggleDropdown(backup.id)"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-500 rounded-full hover:bg-gray-100"
                                                title="Actions"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                                    />
                                                </svg>
                                            </button>

                                            <div
                                                v-show="activeDropdown === backup.id"
                                                class="dropdown-menu absolute right-0 z-50 w-48 mt-2 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                                            >
                                                <div class="py-1">
                                                    <a
                                                        :href="
                                                            route(
                                                                'backup.download',
                                                                backup.filename
                                                            )
                                                        "
                                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                            />
                                                        </svg>
                                                        Download
                                                    </a>
                                                    <button
                                                        @click="
                                                            confirmRestore(
                                                                backup.filename
                                                            )
                                                        "
                                                        class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                                            />
                                                        </svg>
                                                        Restore
                                                    </button>
                                                    <button
                                                        @click="
                                                            confirmDelete(
                                                                backup.filename
                                                            )
                                                        "
                                                        class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !backupsList || backupsList.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="px-6 py-4 text-center text-gray-500"
                                    >
                                        No backup files found
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

<style>
[x-cloak] {
    display: none !important;
}
.dropdown-menu {
  transform-origin: top right;
  transition: all 0.2s ease-out;
}
</style>
