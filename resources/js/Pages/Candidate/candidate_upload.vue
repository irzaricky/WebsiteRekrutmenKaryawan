<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Dashboard from "./Dashboard.vue";

const props = defineProps({
    title: String,
    candidateDetail: Object,
    errors: Object,
});

// Helper to get required ijazah based on education level
const getRequiredIjazah = (level) => {
    switch (level) {
        case "SMA":
            return ["ijazah_smp", "ijazah_sma"];
        case "D3":
            return ["ijazah_smp", "ijazah_sma", "ijazah_d3"];
        case "S1":
            return ["ijazah_smp", "ijazah_sma", "ijazah_s1"];
        case "S2":
            return ["ijazah_smp", "ijazah_sma", "ijazah_s1", "ijazah_s2"];
        case "S3":
            return [
                "ijazah_smp",
                "ijazah_sma",
                "ijazah_s1",
                "ijazah_s2",
                "ijazah_s3",
            ];
        default:
            return [];
    }
};

// Initialize form with all possible fields
const form = useForm({
    // Base documents
    photo: null,
    cv: null,
    // Dynamically add ijazah fields
    ...(props.candidateDetail?.education_level
        ? getRequiredIjazah(props.candidateDetail.education_level).reduce(
              (acc, field) => ({ ...acc, [field]: null }),
              {}
          )
        : {}),
});

// Basic documents (Photo and CV) - unchanged
const baseDocs = computed(() => ({
    photo: { label: "Foto", type: "image" },
    cv: { label: "CV", type: "pdf" },
}));

// Required ijazah - modified to use proper field names
const ijazahDocs = computed(() => {
    const level = props.candidateDetail?.education_level;
    if (!level) return {};

    return getRequiredIjazah(level).reduce(
        (acc, field) => ({
            ...acc,
            [field]: {
                label: `Ijazah ${field.split("_")[1].toUpperCase()}`,
                type: "pdf",
            },
        }),
        {}
    );
});

// Add status check helpers
const isFileAccepted = (type) => {
    return props.candidateDetail?.[`${type}_status`] === "accepted";
};

const isFilePending = (type) => {
    return props.candidateDetail?.[`${type}_status`] === "pending";
};

// Modify handleFileSelect to check status
const handleFileSelect = (event, type) => {
    const file = event.target.files[0];
    if (!file) return;

    // Prevent upload if file is accepted or pending
    if (isFileAccepted(type) || isFilePending(type)) {
        event.target.value = "";
        return;
    }

    form[type] = file;
};

// Add computed property for input state
const getInputState = (type) => {
    if (isFileAccepted(type)) {
        return "accepted";
    } else if (isFilePending(type)) {
        return "pending";
    }
    return "editable";
};

const handleUpload = () => {
    // Create FormData object
    const formData = new FormData();

    // Append base documents
    if (form.photo) formData.append("photo", form.photo);
    if (form.cv) formData.append("cv", form.cv);

    // Append ijazah documents
    Object.entries(ijazahDocs.value).forEach(([type, _]) => {
        if (form[type]) {
            formData.append(type, form[type]);
        }
    });

    form.post(route("candidate.files.update"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            document.querySelectorAll('input[type="file"]').forEach((input) => {
                input.value = "";
            });
        },
        onError: (errors) => {
            console.error("Upload errors:", errors);
        },
    });
};
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
                <!-- Header with improved typography -->
                <div class="mb-10">
                    <h1
                        class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Upload Document
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Please upload the required documents in the correct
                        format.
                    </p>
                </div>

                <!-- Enhanced Flash Messages with icons -->
                <div
                    v-if="$page.props.flash.message"
                    :class="{
                        'bg-yellow-50 border-yellow-200 text-yellow-800':
                            $page.props.flash.type === 'warning',
                        'bg-green-50 border-green-200 text-green-800':
                            $page.props.flash.type === 'success',
                        'bg-red-50 border-red-200 text-red-800':
                            $page.props.flash.type === 'error',
                    }"
                    class="p-4 mb-8 rounded-lg border flex items-center gap-3 shadow-sm"
                >
                    <!-- Dynamic icon based on message type -->
                    <div class="flex-shrink-0">
                        <svg
                            v-if="$page.props.flash.type === 'success'"
                            class="h-5 w-5 text-green-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else-if="$page.props.flash.type === 'warning'"
                            class="h-5 w-5 text-yellow-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5 text-red-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <p class="text-sm font-medium">
                        {{ $page.props.flash.message }}
                    </p>
                </div>

                <!-- Enhanced Validation Errors with better spacing and icons -->
                <div
                    v-if="Object.keys(form.errors).length > 0"
                    class="mb-8 bg-red-50 border border-red-200 rounded-lg p-6 shadow-sm"
                >
                    <div class="flex items-center gap-3 mb-4">
                        <svg
                            class="h-5 w-5 text-red-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <h3 class="text-lg font-medium text-red-800">
                            Please fix the following errors
                        </h3>
                    </div>
                    <ul class="space-y-2 ml-6">
                        <li
                            v-for="(error, key) in form.errors"
                            :key="key"
                            class="flex items-center gap-2 text-sm text-red-700"
                        >
                            <span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"
                            ></span>
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="handleUpload"
                    class="bg-white rounded-xl shadow-lg"
                    enctype="multipart/form-data"
                >
                    <div class="p-6 sm:p-8 space-y-10">
                        <!-- Base Documents (Photo and CV) -->
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-6"
                            >
                                Basic Documents
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    v-for="(doc, type) in baseDocs"
                                    :key="type"
                                    class="bg-white p-6 rounded-xl shadow-md"
                                >
                                    <div
                                        class="flex items-center justify-between mb-4"
                                    >
                                        <label
                                            class="text-xl font-semibold text-gray-900"
                                        >
                                            {{ doc.label }}
                                            <span
                                                v-if="
                                                    props.candidateDetail?.[
                                                        `${type}_status`
                                                    ]
                                                "
                                                :class="{
                                                    'text-red-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'rejected',
                                                    'text-green-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'accepted',
                                                    'text-yellow-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'pending',
                                                }"
                                            >
                                                ({{
                                                    props.candidateDetail[
                                                        `${type}_status`
                                                    ]
                                                }})
                                            </span>
                                        </label>
                                    </div>

                                    <div class="mt-4">
                                        <input
                                            type="file"
                                            :accept="
                                                doc.type === 'image'
                                                    ? '.jpg,.jpeg,.png'
                                                    : '.pdf'
                                            "
                                            @change="
                                                (e) => handleFileSelect(e, type)
                                            "
                                            :name="type"
                                            :disabled="
                                                isFileAccepted(type) ||
                                                isFilePending(type)
                                            "
                                            :class="[
                                                'block w-full text-sm text-gray-500',
                                                'file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0',
                                                'file:text-sm file:font-semibold',
                                                isFileAccepted(type) ||
                                                isFilePending(type)
                                                    ? 'cursor-not-allowed opacity-60'
                                                    : 'file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100',
                                            ]"
                                        />
                                        <p
                                            v-if="isFileAccepted(type)"
                                            class="mt-1 text-sm text-green-600"
                                        >
                                            This file has been accepted and
                                            cannot be changed
                                        </p>
                                        <p
                                            v-if="isFilePending(type)"
                                            class="mt-1 text-sm text-yellow-600"
                                        >
                                            This file is pending review and
                                            cannot be changed
                                        </p>
                                    </div>

                                    <div
                                        v-if="
                                            props.candidateDetail?.[
                                                `${type}_path`
                                            ]
                                        "
                                        class="mt-4"
                                    >
                                        <a
                                            :href="
                                                route('candidate.file', {
                                                    type: type,
                                                    filename:
                                                        props.candidateDetail[
                                                            `${type}_path`
                                                        ]
                                                            .split('/')
                                                            .pop(),
                                                })
                                            "
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            View {{ doc.label }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ijazah Documents -->
                        <div
                            v-if="Object.keys(ijazahDocs).length > 0"
                            class="mt-8"
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-6"
                            >
                                Ijazah Documents
                            </h3>
                            <div class="grid grid-cols-3 gap-6">
                                <div
                                    v-for="(doc, type) in ijazahDocs"
                                    :key="type"
                                    class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-200"
                                >
                                    <div class="flex flex-col space-y-4">
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <label
                                                class="text-lg font-medium text-gray-900"
                                            >
                                                {{ doc.label }}
                                            </label>
                                            <span
                                                v-if="
                                                    props.candidateDetail?.[
                                                        `${type}_status`
                                                    ]
                                                "
                                                :class="{
                                                    'px-2 py-1 rounded-full text-xs font-medium': true,
                                                    'bg-red-100 text-red-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'rejected',
                                                    'bg-green-100 text-green-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'accepted',
                                                    'bg-yellow-100 text-yellow-600':
                                                        props.candidateDetail[
                                                            `${type}_status`
                                                        ] === 'pending',
                                                }"
                                            >
                                                {{
                                                    props.candidateDetail[
                                                        `${type}_status`
                                                    ]
                                                }}
                                            </span>
                                        </div>

                                        <input
                                            type="file"
                                            accept=".pdf"
                                            @change="
                                                (e) => handleFileSelect(e, type)
                                            "
                                            :name="type"
                                            :disabled="
                                                isFileAccepted(type) ||
                                                isFilePending(type)
                                            "
                                            :class="[
                                                'block w-full text-sm text-gray-500',
                                                'file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0',
                                                'file:text-sm file:font-semibold',
                                                isFileAccepted(type) ||
                                                isFilePending(type)
                                                    ? 'cursor-not-allowed opacity-60'
                                                    : 'file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100',
                                            ]"
                                        />

                                        <!-- Status messages -->
                                        <p
                                            v-if="isFileAccepted(type)"
                                            class="text-sm text-green-600"
                                        >
                                            This file has been accepted and
                                            cannot be changed
                                        </p>
                                        <p
                                            v-if="isFilePending(type)"
                                            class="text-sm text-yellow-600"
                                        >
                                            This file is pending review and
                                            cannot be changed
                                        </p>

                                        <div
                                            v-if="
                                                props.candidateDetail?.[
                                                    `${type}_path`
                                                ]
                                            "
                                            class="flex items-center space-x-2"
                                        >
                                            <a
                                                :href="
                                                    route('candidate.file', {
                                                        type: type,
                                                        filename:
                                                            props.candidateDetail[
                                                                `${type}_path`
                                                            ]
                                                                .split('/')
                                                                .pop(),
                                                    })
                                                "
                                                target="_blank"
                                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                                            >
                                                <span class="mr-2"
                                                    >View {{ doc.label }}</span
                                                >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                                    />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div
                            class="flex justify-end pt-8 border-t border-gray-200"
                        >
                            <div class="flex space-x-4">
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
    </Dashboard>
</template>
