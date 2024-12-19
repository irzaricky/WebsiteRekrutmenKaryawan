<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import Sidebar from "@/components/Dashboard/Sidebar.vue";
import { onMounted, ref } from "vue";

const props = defineProps({
    title: String,
    backups: {
        type: Array,
        default: () => [],
    },
    flash: Object,
});

const backupsList = ref(props.backups);

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

onMounted(() => {
    console.log("Backups:", props.backups);
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
            <div class="mb-6 flex gap-4">
                <Link
                    :href="route('backup.create')"
                    method="post"
                    as="button"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Create Backup
                </Link>
            </div>

            <!-- Backups Table -->
            <div class="bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Filename
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Category
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Size
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Created By
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Created At
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="backup in backupsList" :key="backup.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ backup.filename }}
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
                                {{ formatFileSize(backup.size) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ backup.created_by }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ backup.created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <a
                                        :href="
                                            route(
                                                'backup.download',
                                                backup.filename
                                            )
                                        "
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Download
                                    </a>
                                    <button
                                        @click="confirmRestore(backup.filename)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Restore
                                    </button>
                                    <Link
                                        :href="
                                            route(
                                                'backup.delete',
                                                backup.filename
                                            )
                                        "
                                        method="delete"
                                        as="button"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <!-- Show message if no backups -->
                        <tr v-if="!backupsList || backupsList.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                No backup files found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Sidebar>
</template>
