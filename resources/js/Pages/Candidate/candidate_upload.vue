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
