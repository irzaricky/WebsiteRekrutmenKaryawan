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
    <div>
        <div class="container mx-auto p-6">
            <!-- Rest of your existing template code -->
            <Head :title="title" />
            <div class="container mx-auto p-6">
                <h1 class="text-2xl font-bold mb-6">Upload Dokumen</h1>

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
                    class="p-4 mb-4 rounded border"
                >
                    {{ $page.props.flash.message }}
                </div>

                <!-- Validation Errors -->
                <div
                    v-if="Object.keys(form.errors).length > 0"
                    class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-4"
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

                <form
                    @submit.prevent="submit"
                    class="space-y-6 bg-white rounded-lg shadow p-6"
                    enctype="multipart/form-data"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Photo Upload -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Foto</label
                            >
                            <div v-if="photoUrl" class="mt-2 mb-2">
                                <a
                                    :href="photoUrl"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800"
                                >
                                    <img
                                        :src="photoUrl"
                                        alt="Preview"
                                        class="w-32 h-32 object-cover rounded-md"
                                    />
                                    <span class="text-sm"
                                        >View uploaded photo</span
                                    >
                                </a>
                            </div>
                            <input
                                type="file"
                                @input="handlePhotoUpload"
                                accept="image/*"
                                class="mt-1 block w-full"
                            />
                            <span v-if="photoName" class="text-sm text-gray-600"
                                >Selected: {{ photoName }}</span
                            >
                        </div>

                        <!-- CV Upload -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >CV (PDF)</label
                            >
                            <div v-if="cvUrl" class="mt-2 mb-2">
                                <a
                                    :href="cvUrl"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800"
                                >
                                    <svg
                                        class="w-8 h-8 inline-block mr-2"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M4 18h12a2 2 0 002-2V6a2 2 0 00-2-2h-5.586l-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span>View uploaded CV</span>
                                </a>
                            </div>
                            <input
                                type="file"
                                @input="handleCVUpload"
                                accept=".pdf"
                                class="mt-1 block w-full"
                            />
                            <span v-if="cvName" class="text-sm text-gray-600"
                                >Selected: {{ cvName }}</span
                            >
                        </div>

                        <!-- Certificate Upload -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Ijazah (PDF)</label
                            >
                            <div v-if="certificateUrl" class="mt-2 mb-2">
                                <a
                                    :href="certificateUrl"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800"
                                >
                                    <svg
                                        class="w-8 h-8 inline-block mr-2"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M4 18h12a2 2 0 002-2V6a2 2 0 00-2-2h-5.586l-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span>View uploaded certificate</span>
                                </a>
                            </div>
                            <input
                                type="file"
                                @input="handleCertificateUpload"
                                accept=".pdf"
                                class="mt-1 block w-full"
                            />
                            <span
                                v-if="certificateName"
                                class="text-sm text-gray-600"
                                >Selected: {{ certificateName }}</span
                            >
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <Link
                            :href="route('candidate.file-status')"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
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
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            {{
                                form.processing
                                    ? "Uploading..."
                                    : "Upload Files"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
