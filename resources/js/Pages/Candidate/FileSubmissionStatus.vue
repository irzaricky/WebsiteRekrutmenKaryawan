<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";

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
    form.post(route("candidate.files.update"), {
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

// Add computed property to check if all files are accepted
const allFilesAccepted = computed(() => {
    return (
        props.candidateDetail?.photo_status === "accepted" &&
        props.candidateDetail?.cv_status === "accepted" &&
        props.candidateDetail?.certificate_status === "accepted"
    );
});

// Check if profile exists
const hasProfile = computed(() => {
    return !!props.candidateDetail?.nik; // Check for required profile field
});

// Add computed property to disable upload
const canUpload = computed(() => {
    return hasProfile.value;
});
</script>

<template>
    <div>
        <div class="container mx-auto p-6">
            <!-- Header with both buttons -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Document Status</h1>
                <div class="flex gap-4">
                    <Link
                        :href="route('home')"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
                    >
                        Back
                    </Link>
                </div>
            </div>

            <!-- Profile Required Warning -->
            <div
                v-if="!hasProfile"
                class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6"
            >
                <p class="font-bold">Profile Data Required</p>
                <p>
                    Please complete your profile information before uploading
                    documents.
                </p>
                <Link
                    :href="route('candidate.profile')"
                    class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Complete Profile
                </Link>
            </div>

            <!-- Upload Document Button -->
            <div v-else-if="!allFilesAccepted" class="mb-6">
                <Link
                    :href="route('candidate.upload')"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Upload Document
                </Link>
            </div>

            <!-- Rest of existing template code -->
            <div class="space-y-6">
                <!-- Photo Status -->
                <div
                    class="bg-white p-6 rounded-lg shadow"
                    :class="{ 'opacity-50': !hasProfile }"
                >
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
                                props.candidateDetail?.photo_status ||
                                "Not uploaded"
                            }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ getStatusMessage(candidateDetail?.photo_status) }}
                    </p>
                    <div v-if="props.candidateDetail?.photo_path" class="mt-4">
                        <a
                            :href="
                                route('candidate.file', {
                                    type: 'photo',
                                    filename: props.candidateDetail.photo_path
                                        .split('/')
                                        .pop(),
                                })
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            View Photo
                        </a>
                    </div>
                </div>

                <!-- CV Status -->
                <div
                    class="bg-white p-6 rounded-lg shadow"
                    :class="{ 'opacity-50': !hasProfile }"
                >
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
                <div
                    class="bg-white p-6 rounded-lg shadow"
                    :class="{ 'opacity-50': !hasProfile }"
                >
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
    </div>
</template>
