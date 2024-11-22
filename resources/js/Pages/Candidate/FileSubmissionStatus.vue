<script setup>
import { useForm } from "@inertiajs/vue3";
import Sidebar from "../../components/Dashboard/Sidebar.vue";

const props = defineProps({
    title: String,
    candidateDetail: Object,
});

const form = useForm({
    photo: null,
    cv: null,
    certificate: null,
});

const submit = () => {
    form.post(route("candidate.files.upload"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("photo", "cv", "certificate");
        },
    });
};

const removeFile = async (type) => {
    try {
        await axios.delete(route("candidate.file.delete"), {
            data: { type },
        });
        window.location.reload();
    } catch (error) {
        console.error("Error removing file:", error);
    }
};

const getStatusBadgeClass = (status) => {
    return (
        {
            pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
            accepted: "bg-green-100 text-green-800 border-green-200",
            rejected: "bg-red-100 text-red-800 border-red-200",
        }[status] || "bg-gray-100 text-gray-800 border-gray-200"
    );
};

const getStatusMessage = (status) => {
    return (
        {
            pending: "File sedang dalam peninjauan oleh HRD",
            accepted: "File telah diterima dan diverifikasi oleh HRD",
            rejected: "File ditolak. Silakan upload ulang file yang sesuai",
        }[status] || "Status tidak diketahui"
    );
};
</script>

<template>
    <Sidebar>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Document Upload</h1>

            <!-- File upload form -->
            <form
                @submit.prevent="submit"
                class="mb-8 bg-white rounded-lg shadow p-6"
            >
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- File upload fields... -->
                </div>
                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Upload Files
                    </button>
                </div>
            </form>

            <!-- Status cards -->
            <div class="space-y-6">
                <!-- Photo Status -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Foto</h3>
                        <span
                            :class="[
                                'px-3 py-1 rounded-full text-sm border',
                                getStatusBadgeClass(
                                    candidateDetail?.photo_status
                                ),
                            ]"
                        >
                            {{
                                candidateDetail?.photo_status || "Not uploaded"
                            }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ getStatusMessage(candidateDetail?.photo_status) }}
                    </p>
                    <div v-if="candidateDetail?.photo_path" class="mt-4">
                        <img
                            :src="
                                route('candidate.file', {
                                    type: 'photo',
                                    filename: candidateDetail.photo_path
                                        .split('/')
                                        .pop(),
                                })
                            "
                            class="h-32 w-32 object-cover rounded"
                        />
                    </div>
                </div>

                <!-- CV Status -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">CV</h3>
                        <span
                            :class="[
                                'px-3 py-1 rounded-full text-sm border',
                                getStatusBadgeClass(candidateDetail?.cv_status),
                            ]"
                        >
                            {{ candidateDetail?.cv_status || "Not uploaded" }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ getStatusMessage(candidateDetail?.cv_status) }}
                    </p>
                    <div v-if="candidateDetail?.cv_path" class="mt-4">
                        <a
                            :href="
                                route('candidate.file', {
                                    type: 'cv',
                                    filename: candidateDetail.cv_path
                                        .split('/')
                                        .pop(),
                                })
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            View CV
                        </a>
                    </div>
                </div>

                <!-- Certificate Status -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Ijazah</h3>
                        <span
                            :class="[
                                'px-3 py-1 rounded-full text-sm border',
                                getStatusBadgeClass(
                                    candidateDetail?.certificate_status
                                ),
                            ]"
                        >
                            {{
                                candidateDetail?.certificate_status ||
                                "Not uploaded"
                            }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{
                            getStatusMessage(
                                candidateDetail?.certificate_status
                            )
                        }}
                    </p>
                    <div v-if="candidateDetail?.certificate_path" class="mt-4">
                        <a
                            :href="
                                route('candidate.file', {
                                    type: 'certificate',
                                    filename: candidateDetail.certificate_path
                                        .split('/')
                                        .pop(),
                                })
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            View Certificate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
