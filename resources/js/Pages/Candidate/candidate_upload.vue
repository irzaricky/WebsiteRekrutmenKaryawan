<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";

const props = defineProps({
    title: String,
    candidateDetail: Object,
    errors: Object,
});

const form = useForm({
    photo: null,
    cv: null,
    certificate: null,
});

// Add refs for file names and preview URLs
const photoName = ref("");
const cvName = ref("");
const certificateName = ref("");

// Computed properties for file URLs
const photoUrl = computed(() => {
    if (props.candidateDetail?.photo_path) {
        return route("candidate.file", {
            type: "photo",
            filename: props.candidateDetail.photo_path.split("/").pop(),
        });
    }
    return null;
});

const cvUrl = computed(() => {
    if (props.candidateDetail?.cv_path) {
        return route("candidate.file", {
            type: "cv",
            filename: props.candidateDetail.cv_path.split("/").pop(),
        });
    }
    return null;
});

const certificateUrl = computed(() => {
    if (props.candidateDetail?.certificate_path) {
        return route("candidate.file", {
            type: "certificate",
            filename: props.candidateDetail.certificate_path.split("/").pop(),
        });
    }
    return null;
});

// File handler functions
const handlePhotoUpload = (event) => {
    const file = event.target.files[0];
    form.photo = file;
    photoName.value = file ? file.name : "";
};

const handleCVUpload = (event) => {
    const file = event.target.files[0];
    form.cv = file;
    cvName.value = file ? file.name : "";
};

const handleCertificateUpload = (event) => {
    const file = event.target.files[0];
    form.certificate = file;
    certificateName.value = file ? file.name : "";
};

// Modify getFilename to handle null/undefined paths
const getFilename = (path) => {
    return path ? path.split("/").pop() : "";
};

// Add computed property to check if all files are accepted
const allFilesAccepted = computed(() => {
    return (
        props.candidateDetail?.photo_status === "accepted" &&
        props.candidateDetail?.cv_status === "accepted" &&
        props.candidateDetail?.certificate_status === "accepted"
    );
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
    // Show confirmation dialog with file type specific message
    const fileTypes = {
        photo: "Foto",
        cv: "CV",
        certificate: "Ijazah",
    };

    const confirmed = window.confirm(
        `Anda yakin ingin menghapus ${fileTypes[type]} ini?`
    );

    if (!confirmed) {
        return; // Exit if not confirmed
    }

    try {
        await axios.delete(route("candidate.file.delete"), { data: { type } });
        window.location.reload();
    } catch (error) {
        console.error("Error removing file:", error);
    }
};
</script>

<template>
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
                    Upload Document
                </h1>
            </div>

            <!-- Flash Messages -->
            <div
                v-if="$page.props.flash.message"
                :class="{
                    'bg-yellow-100 border-yellow-400 text-yellow-700':
                        $page.props.flash.type === 'warning',
                    'bg-green-100 border-green-400 text-green-700':
                        $page.props.flash.type === 'success',
                    'bg-red-100 border-red-400 text-red-700':
                        $page.props.flash.type === 'error',
                }"
                class="p-4 mb-8 rounded-lg border"
            >
                {{ $page.props.flash.message }}
            </div>

            <!-- Validation Errors -->
            <div
                v-if="Object.keys(form.errors).length > 0"
                class="mb-8 bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg"
            >
                <p class="font-semibold mb-2">
                    Please fix the following errors:
                </p>
                <ul class="list-disc list-inside">
                    <li v-for="(error, key) in form.errors" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Form -->
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-lg"
                enctype="multipart/form-data"
            >
                <div class="p-6 sm:p-8 space-y-10">
                    <!-- Upload Fields -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6"
                    >
                        <!-- Photo Upload -->
                        <div class="text-center flex flex-col items-center">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-4"
                                >Foto</label
                            >
                            <div
                                class="w-full flex flex-col items-center gap-2"
                            >
                                <div v-if="photoUrl" class="mb-4">
                                    <img
                                        :src="photoUrl"
                                        alt="Preview"
                                        class="w-32 h-32 object-cover rounded-lg shadow mx-auto"
                                    />
                                    <a
                                        :href="photoUrl"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 mt-2 block"
                                    >
                                        View uploaded photo
                                    </a>
                                </div>
                                <label
                                    class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold"
                                >
                                    Choose File
                                    <input
                                        type="file"
                                        @input="handlePhotoUpload"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </label>
                                <span class="text-sm text-gray-500">{{
                                    photoName || "No file chosen"
                                }}</span>
                            </div>
                        </div>

                        <!-- CV Upload -->
                        <div class="text-center flex flex-col items-center">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-4"
                                >CV (PDF)</label
                            >
                            <div
                                class="w-full flex flex-col items-center gap-2"
                            >
                                <div v-if="cvUrl" class="mb-4">
                                    <svg
                                        class="w-8 h-8 mx-auto text-blue-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M4 18h12a2 2 0 002-2V6a2 2 0 00-2-2h-5.586l-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <a
                                        :href="cvUrl"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 mt-2 block"
                                    >
                                        View uploaded CV
                                    </a>
                                </div>
                                <label
                                    class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold"
                                >
                                    Choose File
                                    <input
                                        type="file"
                                        @input="handleCVUpload"
                                        accept=".pdf"
                                        class="hidden"
                                    />
                                </label>
                                <span class="text-sm text-gray-500">{{
                                    cvName || "No file chosen"
                                }}</span>
                            </div>
                        </div>

                        <!-- Certificate Upload -->
                        <div class="text-center flex flex-col items-center">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-4"
                                >Ijazah (PDF)</label
                            >
                            <div
                                class="w-full flex flex-col items-center gap-2"
                            >
                                <div v-if="certificateUrl" class="mb-4">
                                    <svg
                                        class="w-8 h-8 mx-auto text-blue-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M4 18h12a2 2 0 002-2V6a2 2 0 00-2-2h-5.586l-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <a
                                        :href="certificateUrl"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 mt-2 block"
                                    >
                                        View uploaded certificate
                                    </a>
                                </div>
                                <label
                                    class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold"
                                >
                                    Choose File
                                    <input
                                        type="file"
                                        @input="handleCertificateUpload"
                                        accept=".pdf"
                                        class="hidden"
                                    />
                                </label>
                                <span class="text-sm text-gray-500">{{
                                    certificateName || "No file chosen"
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end pt-8 border-t border-gray-200">
                        <div class="flex space-x-4">
                            <Link
                                :href="route('candidate.file-status')"
                                class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out"
                            >
                                Back
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                :class="{
                                    'opacity-50 cursor-not-allowed':
                                        form.processing,
                                }"
                                class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out"
                            >
                                {{
                                    form.processing
                                        ? "Uploading..."
                                        : "Upload Files"
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</template>
