<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";
import Dashboard from "./Dashboard.vue";

const props = defineProps({
    title: String,
    candidateDetail: Object,
});

const form = useForm({
    photo: null,
    cv: null,
});

const submit = () => {
    form.post(route("candidate.files.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("photo", "cv");
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

const getStatusBadgeClass = (status) => ({
    "bg-yellow-100 border-yellow-200 text-yellow-800": status === "pending",
    "bg-green-100 border-green-200 text-green-800": status === "accepted",
    "bg-red-100 border-red-200 text-red-800": status === "rejected",
    "bg-gray-100 border-gray-200 text-gray-800": !status,
});

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
        props.candidateDetail?.cv_status === "accepted"
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

// Add new computed property
const requiredIjazah = computed(() => {
    switch (props.candidateDetail?.education_level) {
        case "SMA":
            return ["smp", "sma"];
        case "D3":
            return ["smp", "sma", "d3"];
        case "S1":
            return ["smp", "sma", "s1"];
        case "S2":
            return ["smp", "sma", "s1", "s2"];
        case "S3":
            return ["smp", "sma", "s1", "s2", "s3"];
        default:
            return [];
    }
});

// Use same requiredDocs computed as candidate_upload.vue
const requiredDocs = computed(() => {
    // ... same implementation as above ...
});

// Add education validation check
const hasEducationInfo = computed(() => {
    return !!(
        props.candidateDetail?.education_level &&
        props.candidateDetail?.major &&
        props.candidateDetail?.institution &&
        props.candidateDetail?.graduation_year
    );
});
</script>

<template>
    <Head :title="title" />
    <Dashboard>
        <main class="min-h-screen py-12 lg:py-16">
            <!-- Gradient background -->
            <div
                class="absolute inset-x-0 top-0 -z-10 transform-gpu overflow-hidden px-36 blur-3xl"
                aria-hidden="true"
            >
                <div
                    class="mx-auto aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"
                    style="
                        clip-path: polygon(
                            74.1% 44.1%,
                            100% 61.6%,
                            97.5% 26.9%,
                            85.5% 0.1%,
                            80.7% 2%,
                            72.5% 32.5%,
                            60.2% 62.4%,
                            52.4% 68.1%,
                            47.5% 58.3%,
                            45.2% 34.5%,
                            27.5% 76.7%,
                            0.1% 64.9%,
                            17.9% 100%,
                            27.6% 76.8%,
                            76.1% 97.7%,
                            74.1% 44.1%
                        );
                    "
                />
            </div>

            <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Document Status
                    </h1>
                </div>

                <!-- Profile Required Warning -->
                <div
                    v-if="!hasProfile"
                    class="mb-8 bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg"
                >
                    <p class="font-bold">Profile Data Required</p>
                    <p class="mt-1">
                        Please complete your profile information before
                        uploading documents.
                    </p>
                    <Link
                        :href="route('candidate.profile')"
                        class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors duration-150 ease-in-out"
                    >
                        Complete Profile
                    </Link>
                </div>

                <!-- Education Required Warning -->
                <div
                    v-if="!hasEducationInfo"
                    class="mb-8 bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg"
                >
                    <p class="font-bold">Education Information Required</p>
                    <p class="mt-1">
                        Please complete your education information before
                        uploading documents.
                    </p>
                    <Link
                        :href="route('candidate.profile')"
                        class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors duration-150 ease-in-out"
                    >
                        Complete Education Info
                    </Link>
                </div>

                <!-- Upload Document Button -->
                <div v-if="hasEducationInfo && !allFilesAccepted" class="mb-8">
                    <Link
                        :href="route('candidate.upload')"
                        class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-150 ease-in-out"
                    >
                        Upload Document
                    </Link>
                </div>

                <!-- Document Status Cards -->
                <div class="space-y-6">
                    <!-- Photo Status -->
                    <div
                        class="bg-white p-8 rounded-xl shadow-lg"
                        :class="{ 'opacity-50': !hasProfile }"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Foto
                            </h3>
                            <span
                                :class="[
                                    'px-4 py-1.5 rounded-full text-sm font-medium border',
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
                            {{
                                getStatusMessage(candidateDetail?.photo_status)
                            }}
                        </p>
                        <div
                            v-if="props.candidateDetail?.photo_path"
                            class="mt-4"
                        >
                            <a
                                :href="
                                    route('candidate.file', {
                                        type: 'photo',
                                        filename:
                                            props.candidateDetail.photo_path
                                                .split('/')
                                                .pop(),
                                    })
                                "
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                View Photo
                            </a>
                        </div>
                    </div>

                    <!-- CV Status -->
                    <div
                        class="bg-white p-8 rounded-xl shadow-lg"
                        :class="{ 'opacity-50': !hasProfile }"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">
                                CV
                            </h3>
                            <span
                                :class="[
                                    'px-4 py-1.5 rounded-full text-sm font-medium border',
                                    getStatusBadgeClass(
                                        candidateDetail?.cv_status
                                    ),
                                ]"
                            >
                                {{
                                    candidateDetail?.cv_status || "Not uploaded"
                                }}
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
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                View CV
                            </a>
                        </div>
                    </div>

                    <!-- Add this after other status cards -->
                    <div
                        v-for="level in requiredIjazah"
                        :key="level"
                        class="bg-white p-8 rounded-xl shadow-lg"
                        :class="{ 'opacity-50': !hasProfile }"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Ijazah {{ level.toUpperCase() }}
                            </h3>
                            <span
                                :class="[
                                    'px-4 py-1.5 rounded-full text-sm font-medium border',
                                    getStatusBadgeClass(
                                        candidateDetail?.[
                                            `ijazah_${level}_status`
                                        ]
                                    ),
                                ]"
                            >
                                {{
                                    candidateDetail?.[
                                        `ijazah_${level}_status`
                                    ] || "Not uploaded"
                                }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{
                                getStatusMessage(
                                    candidateDetail?.[`ijazah_${level}_status`]
                                )
                            }}
                        </p>
                        <div
                            v-if="candidateDetail?.[`ijazah_${level}_path`]"
                            class="mt-4"
                        >
                            <a
                                :href="
                                    route('candidate.file', {
                                        type: `ijazah_${level}`,
                                        filename: candidateDetail[
                                            `ijazah_${level}_path`
                                        ]
                                            .split('/')
                                            .pop(),
                                    })
                                "
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                View Ijazah {{ level.toUpperCase() }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </Dashboard>
</template>
