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
        <main class="min-h-screen">
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
                <!-- Enhanced Header with improved typography and spacing -->
                <div class="mb-12">
                    <h1
                        class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Document Status
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Track the status of your submitted documents
                    </p>
                </div>

                <!-- Profile Required Warning - Enhanced with better visual hierarchy -->
                <div
                    v-if="!hasProfile"
                    class="mb-8 relative overflow-hidden rounded-xl bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-400 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-6 w-6 text-yellow-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-800">
                                Profile Data Required
                            </h3>
                            <p class="mt-1 text-yellow-700">
                                Please complete your profile information before
                                uploading documents.
                            </p>
                            <Link
                                :href="route('candidate.profile')"
                                class="mt-4 inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 text-white px-6 py-2.5 rounded-lg transition-all duration-200 font-medium shadow-sm"
                            >
                                <span>Complete Profile</span>
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Education Required Warning - Enhanced with better visual hierarchy -->
                <div
                    v-if="!hasEducationInfo"
                    class="mb-8 relative overflow-hidden rounded-xl bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-400 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-6 w-6 text-yellow-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-800">
                                Education Information Required
                            </h3>
                            <p class="mt-1 text-yellow-700">
                                Please complete your education information
                                before uploading documents.
                            </p>
                            <Link
                                :href="route('candidate.profile')"
                                class="mt-4 inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 text-white px-6 py-2.5 rounded-lg transition-all duration-200 font-medium shadow-sm"
                            >
                                <span>Complete Education Info</span>
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </Link>
                        </div>
                    </div>
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
